<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/29
 * Time: 10:31
 */

namespace Kuke\Utils;



use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Amqp\Producer;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\JobInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Utils\ApplicationContext;

class Functions
{
    public static function di($id = null)
    {
        $container = ApplicationContext::getContainer();
        if ($id) {
            return $container->get($id);
        }

        return $container;
    }

    /**
     * @param \Throwable $throwable
     * @return string
     */
    public static function format_throwable(\Throwable $throwable):string
    {
        return self::di()->get(FormatterInterface::class)->format($throwable);
    }

    /**
     * @param JobInterface $job
     * @param int $delay
     * @param string $key
     * @return bool
     */
    public static function queue_push(JobInterface $job, int $delay = 0, string $key = 'default'): bool
    {
        $driver = self::di()->get(DriverFactory::class)->get($key);
        return $driver->push($job, $delay);
    }

    public static function amqp_produce(ProducerMessageInterface $message): bool
    {
        return self::di()->get(Producer::class)->produce($message, true);
    }

    function jwt():\Hyperf\JwtAuth\Jwt
    {
        return self::di()->get(\Hyperf\JwtAuth\Jwt::class);
    }
}
