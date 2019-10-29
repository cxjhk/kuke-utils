<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/28
 * Time: 10:45
 */

namespace Kuke\WeChat;
use Kuke\Utils\ArrayHelper;
use Kuke\Utils\Functions;

class  ClientFactory
{
    private $url = 'https://api.weixin.qq.com';

    public function handle($config = [])
    {
        return Functions::di()
            ->get(\Hyperf\Guzzle\ClientFactory::class)
            ->create(ArrayHelper::merge([
                'base_uri'  =>  $this->url
            ],$config));
    }
}
