<?php declare(strict_types = 1);

namespace Kuke\WeChat\MiniProgram\Auth;

use Kuke\WeChat\MiniProgram\ServiceFactory;

class AccessToken extends ServiceFactory
{
    /**
     * @param bool $up
     * @return bool|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken($up = false)
    {

        if ($this->cache('kuke wechat accessToken') && $up == false){
            return $this->cache('kuke wechat accessToken');
        }

        $uri = 'cgi-bin/token?grant_type=client_credential&appid='.$this->config['app_id'].'&secret='.$this->config['secret'];

        $response = $this->client()->get($uri);

        $content = $this->response($response)->content;

        if (empty($content['access_token'])) return $content;

        $this->cache('kuke wechat accessToken',$content,$content['expires_in']);

        return $content;
    }
}
