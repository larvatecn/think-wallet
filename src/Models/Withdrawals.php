<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet\Models;

use Carbon\Carbon;
use Larva\Transaction\Models\Transfer;
use Larva\Wallet\Events\WithdrawalsCanceled;
use Larva\Wallet\Events\WithdrawalsFailure;
use Larva\Wallet\Events\WithdrawalsSuccess;
use think\facade\Event;
use think\facade\Lang;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\MorphOne;

/**
 * 钱包提现明细
 *
 * @property int $user_id 用户ID
 * @property int $amount 金额
 * @property string $status 状态
 * @property string $channel 渠道
 * @property string $recipient 支付凭证
 * @property string $client_ip 客户端IP
 * @property array $metadata
 * @property-read array $extra 提现附加参数
 * @property Carbon|null $created_at
 * @property Carbon|null $canceled_at
 * @property Carbon|null $succeeded_at
 *
 * @property \app\model\User $user
 * @property Wallet $wallet
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Withdrawals extends Model
{
    const STATUS_PENDING = 'pending';//处理中： pending
    const STATUS_SUCCEEDED = 'succeeded';//完成： succeeded
    const STATUS_FAILED = 'failed';//失败： failed
    const STATUS_CANCELED = 'canceled';//取消： canceled

    /**
     * 与模型关联的数据表。
     *
     * @var string
     */
    protected $name = 'wallet_withdrawals';

    /**
     * 属性类型转换
     *
     * @var array
     */
    protected $type = [
        'metadata' => 'array',
    ];

    /**
     * 获取提现附加参数
     * @return array
     */
    public function getExtraAttr(): array
    {
        return [
            //微信
            'type' => $this->metadata['type'] ?? '',
            'user_name' => $this->metadata['name'] ?? '',
            //支付宝
            'recipient_name' => $this->metadata['name'] ?? '',
            'recipient_account_type' => $this->metadata['account_type'] ?? ''
        ];
    }

    /**
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'user_id', 'user_id');
    }

    /**
     * Get the entity's transaction.
     *
     * @return morphOne
     */
    public function transaction(): morphOne
    {
        return $this->morphOne(Transaction::class, 'source');
    }

    /**
     * Get the entity's transfer.
     *
     * @return morphOne
     */
    public function transfer(): morphOne
    {
        return $this->morphOne(Transfer::class, 'order');
    }

    /**
     * 设置提现成功
     */
    public function setSucceeded()
    {
        $this->save(['status' => static::STATUS_SUCCEEDED, 'succeeded_at' => $this->freshTimestamp()]);
        Event::trigger(new WithdrawalsSuccess($this));
    }

    /**
     * 取消提现
     * @return bool
     */
    public function setCanceled(): bool
    {
        $this->transaction()->save([
            'user_id' => $this->user_id,
            'type' => Transaction::TYPE_WITHDRAWAL_REVOKED,
            'description' => Lang::get('wallet.withdrawal_revoked'),
            'amount' => $this->amount,
            'available_amount' => bcadd($this->wallet->available_amount, $this->amount)
        ]);
        $this->update(['status' => static::STATUS_CANCELED, 'canceled_at' => $this->freshTimestamp()]);
        Event::trigger(new WithdrawalsCanceled($this));
        return true;
    }

    /**
     * 提现失败平账
     * @return bool
     */
    public function setFailed(): bool
    {
        $this->transaction()->save([
            'user_id' => $this->user_id,
            'type' => Transaction::TYPE_WITHDRAWAL_FAILED,
            'description' => Lang::get('wallet.withdrawal_failed'),
            'amount' => $this->amount,
            'available_amount' => $this->wallet->available_amount + $this->amount
        ]);
        $this->update(['status' => static::STATUS_FAILED, 'canceled_at' => $this->freshTimestamp()]);
        Event::trigger(new WithdrawalsFailure($this));
        return true;
    }

    /**
     * 状态
     * @return string[]
     */
    public static function getStatusLabels(): array
    {
        return [
            static::STATUS_PENDING => '等待处理',
            static::STATUS_SUCCEEDED => '提现成功',
            static::STATUS_FAILED => '提现失败',
            static::STATUS_CANCELED => '提现撤销',
        ];
    }

    /**
     * 获取状态Dot
     * @return string[]
     */
    public static function getStatusDots(): array
    {
        return [
            static::STATUS_PENDING => 'info',
            static::STATUS_SUCCEEDED => 'success',
            static::STATUS_FAILED => 'warning',
            static::STATUS_CANCELED => 'info',
        ];
    }
}