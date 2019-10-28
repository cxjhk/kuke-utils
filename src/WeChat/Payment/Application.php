<?php declare(strict_types = 1);

namespace Kuke\WeChat\Payment;


use Hyperf\Utils\Context;
use Kuke\WeChat\MiniProgram\AppCode\Client;
use Kuke\WeChat\MiniProgram\Auth\AccessToken;

/**
 * Class Factory.
 * @property \Kuke\WeChat\Payment\Jssdk\Client           $jssdk
 * @property \Kuke\WeChat\Payment\Order\Client           $order
 * @property \Kuke\WeChat\Payment\Refund\Client          $refund
 */
class Application
{

    public function __construct($config)
    {
        if (!Context::has('kukehuyu wechat config')) {
            Context::set('kukehuyu wechat config',$config);
        }
    }

    private $classes =[
        'jssdk'      =>  \Kuke\WeChat\Payment\Jssdk\Client::class,
        'order'      =>  \Kuke\WeChat\Payment\Order\Client::class,
        'refund'     =>  \Kuke\WeChat\Payment\Refund\Client::class,
    ];

    public function __get($name)
    {
        return new $this->classes[$name];
    }
}
