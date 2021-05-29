<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Wallet\Listeners;

use Larva\Transaction\Events\ChargeShipped;
use Larva\Wallet\Models\Recharge;

class ChargeShippedListener
{
    /**
     * Handle the event.
     *
     * @param ChargeShipped $event
     * @return void
     */
    public function handle(ChargeShipped $event)
    {
        if ($event->charge->source instanceof Recharge) {//充值成功
            $event->charge->source->setSucceeded();
        }
    }
}