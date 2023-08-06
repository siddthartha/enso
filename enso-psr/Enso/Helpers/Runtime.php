<?php
declare(strict_types = 1);
/**
 * Class Enso\Helpers\Runtime
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Helpers;

use Swoole\Coroutine;

/**
 * Description of Runtime
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
final class Runtime
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_FATAL = -1;

    /**
     * PHP built-in server routing support
     *
     * @return bool
     */
    public static function isSapiAsIsHandled(): bool
    {
        if (PHP_SAPI === 'cli-server')
        {
            // Serve static files AS IS
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (is_file(__DIR__ . $path))
            {
                return true;
            }

            // Explicitly set for URLs with dot
            $_SERVER['SCRIPT_NAME'] = '/index.php';
        }

        return false;
    }

    /**
     * C3 Codeception coverage support
     *
     * @return void
     */
    public static function supportC3(): void
    {
        if ($_ENV['YII_ENV'] !== 'test')
        {
            return;
        }

        $c3 = dirname(__DIR__) . '/../c3.php';

        if (file_exists($c3))
        {
            require_once $c3;
        }
    }

    /**
     * Aren't we inside a CLI call now?
     *
     * @return bool
     */
    public static function isCLI(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Aren't we inside an FPM call now?
     *
     * @return bool
     */
    public static function isFPM(): bool
    {
        return PHP_SAPI === 'fpm-fcgi';
    }

    /**
     * Aren't we inside Swoole now?
     * @TODO: improve
     *
     * @return bool
     */
    public static function haveSwoole(): bool
    {
        return function_exists('swoole_version') && is_string(swoole_version());
    }

    public static function isDaemon(): bool
    {
        return (Runtime::haveSwoole() && Coroutine::getCid() !== -1)
            || Runtime::isGoridge();
    }

    public static function isGoridge(): bool
    {
        return isset($_ENV['RR_MODE']);
    }
}