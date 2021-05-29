<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Wallet\Listeners;

use Larva\Transaction\Events\ChargeClosed;
use Larva\Wallet\Models\Recharge;

class ChargeClosedListener
{
    /**
     * 事件监听处理
     *
     * @return void
     */
    public function handle(ChargeClosed $event)
    {
        if ($event->charge->source instanceof Recharge) {//充值关闭
            $event->charge->source->setFailure();
        }
    }
}