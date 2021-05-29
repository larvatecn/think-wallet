<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet\Models;

use Carbon\Carbon;
use Larva\Transaction\Models\Charge;
use Larva\Wallet\Events\RechargeFailure;
use Larva\Wallet\Events\RechargeShipped;
use think\facade\Event;
use think\facade\Lang;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\MorphOne;

/**
 * 钱包充值明细
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string $channel
 * @property string $type
 * @property string $status
 * @property string $client_ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $succeeded_at
 *
 * @property Charge $charge
 * @property \app\model\User $user
 * @property Transaction $transaction
 * @property Wallet $wallet
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Recharge extends Model
{
    const STATUS_PENDING = 'pending';//处理中： pending
    const STATUS_SUCCEEDED = 'succeeded';//完成： succeeded
    const STATUS_FAILED = 'failed';//失败： failed

    /**
     * 与模型关联的数据表。
     *
     * @var string
     */
    protected $name = 'wallet_recharges';

    /**
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'user_id', 'user_id');
    }

    /**
     * 关联用户模型
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('transaction.user'), 'user_id');
    }

    /**
     * Get the entity's transaction.
     *
     * @return MorphOne
     */
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'source');
    }

    /**
     * Get the entity's charge.
     *
     * @return MorphOne
     */
    public function charge(): MorphOne
    {
        return $this->morphOne(Charge::class, 'source');
    }

    /**
     * 设置交易成功
     */
    public function setSucceeded(): bool
    {
        $status = $this->save(['channel' => $this->charge->channel, 'type' => $this->charge->type, 'status' => static::STATUS_SUCCEEDED, 'succeeded_at' => $this->freshTimestamp()]);
        $this->transaction()->save([
            'user_id' => $this->user_id,
            'type' => Transaction::TYPE_RECHARGE,
            'description' => Lang::get('wallet.wallet_recharge'),
            'amount' => $this->amount,
            'available_amount' => $this->wallet->available_amount + $this->amount
        ]);
        Event::trigger(new RechargeShipped($this));
        return $status;
    }

    /**
     * 设置交易失败
     */
    public function setFailure(): bool
    {
        $status = $this->save(['status' => static::STATUS_FAILED]);
        Event::trigger(new RechargeFailure($this));
        return $status;
    }

    /**
     * 状态
     * @return string[]
     */
    public static function getStatusLabels(): array
    {
        return [
            static::STATUS_PENDING => '等待付款',
            static::STATUS_SUCCEEDED => '充值成功',
            static::STATUS_FAILED => '充值失败',
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
        ];
    }
}