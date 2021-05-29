<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet\Models;

use Carbon\Carbon;
use Larva\Wallet\Exceptions\WalletException;
use think\Model;
use think\model\relation\HasMany;

/**
 * 钱包
 * @property int $user_id
 * @property int $available_amount 可用金额
 * @property Carbon|null $created_at 钱包创建时间
 * @property Carbon|null $updated_at 钱包更新时间
 *
 * @property \Illuminate\Foundation\Auth\User $user
 * @property Recharge[] $recharges 钱包充值记录
 * @property Transaction[] $transactions 钱包交易记录
 * @property Withdrawals[] $withdrawals 钱包提现记录
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Wallet extends Model
{
    /**
     * 获取指定用户钱包
     * @param int $user_id
     */
    public function scopeUserId($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * 钱包充值明细
     * @return hasMany
     */
    public function recharges(): hasMany
    {
        return $this->hasMany(Recharge::class, 'user_id', 'user_id');
    }

    /**
     * 钱包交易明细
     * @return hasMany
     */
    public function transactions(): hasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    /**
     * 钱包提现明细
     * @return hasMany
     */
    public function withdrawals(): hasMany
    {
        return $this->hasMany(Withdrawals::class, 'user_id', 'user_id');
    }

    /**
     * 创建充值请求
     * @param string $channel 渠道
     * @param int $amount 金额 单位分
     * @param string $type 支付类型
     * @param string $clientIP 客户端IP
     * @return Recharge
     */
    public function rechargeAction(string $channel, $amount, $type, $clientIP = null)
    {
        return $this->recharges()->save(['channel' => $channel, 'amount' => $amount, 'type' => $type, 'client_ip' => $clientIP]);
    }

    /**
     * 创建提现请求
     * @param int $amount
     * @param string $channel
     * @param string $recipient 收款账户
     * @param array $metaData 附加信息
     * @param string|null $clientIP 客户端IP
     * @return Withdrawals
     * @throws WalletException
     */
    public function withdrawalsAction(int $amount, string $channel, string $recipient, array $metaData = [], string $clientIP = null)
    {
        $availableAmount = $this->available_amount - $amount;
        if ($availableAmount < 0) {//计算后如果余额小于0，那么结果不合法。
            throw new WalletException('Insufficient wallet balance.');//钱包余额不足
        }
        return $this->withdrawals()->save([
            'amount' => $amount,
            'channel' => $channel,
            'status' => Withdrawals::STATUS_PENDING,
            'recipient' => $recipient,
            'metadata' => $metaData,
            'client_ip' => $clientIP,
        ]);
    }
}