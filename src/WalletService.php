<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

declare (strict_types=1);

namespace Larva\Wallet;

use think\facade\Event;
use think\Service;

class WalletService extends Service
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Transaction
        Event::listen(\Larva\Transaction\Events\ChargeClosed::class, \Larva\Wallet\Listeners\ChargeClosedListener::class);//支付关闭
        Event::listen(\Larva\Transaction\Events\ChargeFailure::class, \Larva\Wallet\Listeners\ChargeFailureListener::class);//支付失败
        Event::listen(\Larva\Transaction\Events\ChargeShipped::class, \Larva\Wallet\Listeners\ChargeShippedListener::class);//支付成功
        Event::listen(\Larva\Transaction\Events\TransferFailure::class, \Larva\Wallet\Listeners\TransferFailureListener::class);//提现失败
        Event::listen(\Larva\Transaction\Events\TransferShipped::class, \Larva\Wallet\Listeners\TransferShippedListener::class);//提现成功


    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}