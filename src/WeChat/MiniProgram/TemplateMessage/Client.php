<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/28
 * Time: 11:36
 */

namespace Kuke\WeChat\MiniProgram\TemplateMessage;


use Kuke\WeChat\MiniProgram\ServiceFactory;

class Client extends ServiceFactory
{
    /**
     * @param array $params
     * @return mixed
     * @document https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
     */
    public function send(array $params)
    {
        $uri = '/cgi-bin/message/subscribe/send?access_token='.$this->token['access_token'];

        $response = $this->client()->post($uri,[
            'json'   =>  $params
        ]);

        return $this->response($response)->content;
    }
}
