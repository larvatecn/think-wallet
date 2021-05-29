<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet\Models;

use Carbon\Carbon;
use think\facade\Lang;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\MorphTo;

/**
 * 钱包交易明细
 *
 * @property string $id
 * @property int $user_id
 * @property int $amount
 * @property int $available_amount 交易后可用金额
 * @property string $description
 * @property string $type
 * @property-read string $typeName
 * @property string $client_ip
 * @property \app\model\User $user
 * @property Carbon|null $created_at
 *
 * @property Wallet $wallet
 * @property Model $source
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Transaction extends Model
{
    const TYPE_RECHARGE = 'recharge';//充值
    const TYPE_RECHARGE_REFUND = 'recharge_refund';//充值退款
    const TYPE_RECHARGE_REFUND_FAILED = 'recharge_refund_failed';//充值退款失败
    const TYPE_WITHDRAWAL = 'withdrawal';//提现申请
    const TYPE_WITHDRAWAL_FAILED = 'withdrawal_failed';//提现失败
    const TYPE_WITHDRAWAL_REVOKED = 'withdrawal_revoked';//提现撤销
    const TYPE_PAYMENT = 'payment';//支付/收款
    const TYPE_PAYMENT_REFUND = 'payment_refund';//退款/收到退款
    const TYPE_TRANSFER = 'transfer';//转账/收到转账
    const TYPE_RECEIPTS_EXTRA = 'receipts_extra';//赠送
    const TYPE_ROYALTY = 'royalty';//分润/收到分润
    const TYPE_REWARD = 'reward';//奖励/收到奖励

    /**
     * 与模型关联的数据表。
     *
     * @var string
     */
    protected $name = 'wallet_transactions';

    /**
     * 获取所有操作类型
     * @return array
     */
    public static function getAllType(): array
    {
        return [
            static::TYPE_RECHARGE => Lang::get('wallet.' . static::TYPE_RECHARGE),
            static::TYPE_RECHARGE_REFUND => Lang::get('wallet.' . static::TYPE_RECHARGE_REFUND),
            static::TYPE_RECHARGE_REFUND_FAILED => Lang::get('wallet.' . static::TYPE_RECHARGE_REFUND_FAILED),
            static::TYPE_WITHDRAWAL => Lang::get('wallet.' . static::TYPE_WITHDRAWAL),
            static::TYPE_WITHDRAWAL_FAILED => Lang::get('wallet.' . static::TYPE_WITHDRAWAL_FAILED),
            static::TYPE_WITHDRAWAL_REVOKED => Lang::get('wallet.' . static::TYPE_WITHDRAWAL_REVOKED),
            static::TYPE_PAYMENT => Lang::get('wallet.' . static::TYPE_PAYMENT),
            static::TYPE_PAYMENT_REFUND => Lang::get('wallet.' . static::TYPE_PAYMENT_REFUND),
            static::TYPE_TRANSFER => Lang::get('wwallet.' . static::TYPE_TRANSFER),
            static::TYPE_RECEIPTS_EXTRA => Lang::get('wallet.' . static::TYPE_RECEIPTS_EXTRA),
            static::TYPE_ROYALTY => Lang::get('wallet.' . static::TYPE_ROYALTY),
            static::TYPE_REWARD => Lang::get('wallet.' . static::TYPE_REWARD),
        ];
    }

    /**
     * 获取Type名称
     * @return string
     */
    public function getTypeNameAttr(): string
    {
        return Lang::get('wallet.' . $this->type);
    }

    /**
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'user_id', 'user_id');
    }

    /**
     * Get the source entity that the Transaction belongs to.
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 关联用户模型
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('transaction.user'), 'user_id');
    }
}