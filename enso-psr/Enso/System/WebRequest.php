<?php
declare(strict_types = 1);
/**
 * Class Enso\System\WebRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

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
        $path = explode('/', trim($this->getOrigin()->getUri()->getPath(), " \t\n\r\0\x0B\/"));

        return count($path) == 1 && $path[0] == ""
            ? ['default', 'action']
            : $path;
    }
}