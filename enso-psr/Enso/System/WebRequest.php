<?php
declare(strict_types = 1);
/**
 * Class Enso\System\WebRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use mb_ereg_replace;
use count;
use trim;
use explode;

/**
 * Description of WebRequest
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class WebRequest extends \Enso\Relay\Request
{

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        $phpSelfPath = $_SERVER['PHP_SELF'];

        $uriPath = explode(
            '/',
            trim(
                mb_ereg_replace("^($phpSelfPath)", '', $this->getOrigin()->getUri()->getPath()),
                '/'
            )
        );

        return count($uriPath) == 0 || (count($uriPath) == 1 && $uriPath[0] == "")
            ? ['default', 'action']
            : $uriPath;
    }
}