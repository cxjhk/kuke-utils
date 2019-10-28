<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/28
 * Time: 11:17
 */

namespace Kuke\WeChat\MiniProgram;


use Hyperf\Utils\Context;
use Kuke\WeChat\MiniProgram\AppCode\Client;
use Kuke\WeChat\MiniProgram\Auth\AccessToken;

/**
 * Class Factory.
 * @property \Kuke\WeChat\MiniProgram\Auth\AccessToken           $access_token
 * @property \Kuke\WeChat\MiniProgram\AppCode\Client             $app_code
 * @property \Kuke\WeChat\MiniProgram\Auth\Client                $auth
 * @property \Kuke\WeChat\MiniProgram\Encryptor                  $encryptor
 * @property \Kuke\WeChat\MiniProgram\TemplateMessage\Client     $template_message
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
        'access_token'      =>  AccessToken::class,
        'app_code'          =>  Client::class,
        'auth'              =>  \Kuke\WeChat\MiniProgram\Auth\Client::class,
        'encryptor'         =>  Encryptor::class,
        'template_message'  =>  \Kuke\WeChat\MiniProgram\TemplateMessage\Client::class
    ];

    public function __get($name)
    {
        return new $this->classes[$name];
    }
}
