<?php
declare(strict_types = 1);
/**
 * Class Enso\Helpers\Runtime
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Helpers;

/**
 * Description of Runtime
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
final class Runtime
{
    /**
     * PHP built-in server routing support
     *
     * @return void
     */
    static public function isSapiAsIsHandled(): bool
    {
        if (PHP_SAPI === 'cli-server')
        {
            // Serve static files as is.
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (is_file(__DIR__ . $path))
            {
                return true;
            }

            // Explicitly set for URLs with dot.
            $_SERVER['SCRIPT_NAME'] = '/index.php';
        }

        return false;
    }

    /**
     * C3 Codeception coverage support
     *
     * @return void
     */
    static public function supportC3(): void
    {
        if ($_ENV['YII_ENV'] !== 'test')
        {
            return;
        }

        $c3 = dirname(__DIR__) . '/c3.php';

        if (file_exists($c3))
        {
            require_once $c3;
        }
    }
}