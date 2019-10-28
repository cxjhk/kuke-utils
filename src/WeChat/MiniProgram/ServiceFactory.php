<?php declare(strict_types = 1);

namespace Kuke\WeChat\MiniProgram;
use Hyperf\Utils\Context;
use Kuke\WeChat\ClientFactory;
use Kuke\WeChat\MiniProgram\Auth\AccessToken;
use Psr\SimpleCache\CacheInterface;

class ServiceFactory
{
    public $config;

    public $content;

    public function __construct()
    {
        $this->config = Context::get('kukehuyu wechat config');
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return \GuzzleHttp\Client
     */
    public function client($config = [])
    {
        return di(ClientFactory::class)->handle($config);
    }

    /**
     * @param string $message
     * @return array
     */
    public function fail($message = '')
    {
        return ['code'=>400,'message'=>$message];
    }

    /**
     * @param $response
     * @return $this
     */
    public function response($response)
    {
        $this->content = json_decode($response->getBody()->getContents(),true);
        return $this;
    }

    /**
     * 缓存
     * @param $key
     * @param string $value
     * @param null $tll
     * @return bool|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cache($key,$value = '',$tll = null)
    {
        $cache = di(CacheInterface::class);
        if (is_null($value)){
            return $cache->delete($key);
        }elseif(empty($value)){
            return $cache->get($key);
        }elseif(!empty($value) || !is_null($value)){
            return $cache->set($key,$value,$tll);
        }
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        if ($name == 'token'){
            return di(AccessToken::class)->getToken();
        }
    }
}
