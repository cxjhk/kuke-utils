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
    public function send(array $params)
    {
        $uri = '/cgi-bin/message/subscribe/send?access_token='.$this->token['access_token'];

    }
}
