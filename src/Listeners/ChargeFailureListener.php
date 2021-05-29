<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet\Listeners;

use Larva\Transaction\Events\ChargeFailure;
use Larva\Wallet\Models\Recharge;

class ChargeFailureListener
{
    /**
     * Handle the event.
     *
     * @param ChargeFailure $event
     * @return void
     */
    public function handle(ChargeFailure $event)
    {
        if ($event->charge->source instanceof Recharge) {//充值失败
            $event->charge->source->setFailure();
        }
    }
}