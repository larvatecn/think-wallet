<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Wallet\Events;

use Larva\Wallet\Models\Withdrawals;

/**
 * 取消提现事件
 * @author Tongle Xu <xutongle@gmail.com>
 */
class WithdrawalsCanceled
{
    /**
     * @var Withdrawals
     */
    public $withdrawals;

    /**
     * RefundFailure constructor.
     * @param Withdrawals $withdrawals
     */
    public function __construct(Withdrawals $withdrawals)
    {
        $this->withdrawals = $withdrawals;
    }
}