<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Wallet\Listeners;

use Larva\Transaction\Events\TransferShipped;
use Larva\Wallet\Models\Withdrawals;

class TransferShippedListener
{
    /**
     * Handle the event.
     *
     * @param TransferShipped $event
     * @return void
     */
    public function handle(TransferShipped $event)
    {
        if ($event->transfer->source instanceof Withdrawals) {
            $event->transfer->source->setSucceeded();
        }
    }
}