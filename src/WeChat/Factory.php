<?php declare(strict_types=1);

namespace Kuke\WeChat;

/**
 * Class Factory.
 *
 * @method static \Kuke\WeChat\MiniProgram\Application        miniProgram(array $config)
 * @method static \Kuke\WeChat\Payment\Application            payment(array  $config)
 */
class Factory
{
    /**
     * @param $name
     * @param array $config
     * @return mixed
     */
    public static function make($name, array $config)
    {
        $className = ucwords($name);
        $application = "\\Kuke\\WeChat\\{$className}\\Application";

        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}
