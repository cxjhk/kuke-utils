<?php declare(strict_types = 1);

namespace Kuke\WeChat\MiniProgram\Auth;

use Kuke\WeChat\MiniProgram\ServiceFactory;

/**
 * WeChat Auth
 * @package Kuke\WeChat\MiniProgram\Auth
 */
class Client extends ServiceFactory
{
    /**
     * 小程序授权登录
     * @param string $code
     * @return mixed
     */
    public function session(string $code)
    {
        $uri = '/sns/jscode2session?appid='.$this->config['app_id'].'&secret='.$this->config['secret'].'&js_code='.$code.'&grant_type=authorization_code';

        $response = $this->client()->get($uri);

        return $this->response($response)->content;
    }
}
