<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet\Listeners;

use Larva\Transaction\Events\TransferFailure;
use Larva\Wallet\Models\Withdrawals;

class TransferFailureListener
{
    /**
     * Handle the event.
     *
     * @param TransferFailure $event
     * @return void
     */
    public function handle(TransferFailure $event)
    {
        if ($event->transfer->source instanceof Withdrawals) {
            $event->transfer->source->setFailed();
        }
    }
}