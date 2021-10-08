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
    public function __construct()
    {
        parent::__construct([]);

        $this->post = $_POST;
        $this->get = $_GET;

        [
            $this->uri,
            $this->query
        ] = explode('?', $_SERVER['REQUEST_URI'] . "?");

        $this->route = $this->getRoute();
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        $path = explode('/', trim($this->uri, " \t\n\r\0\x0B\/"));

        return count($path) == 1 && $path[0] == ""
            ? []
            : $path;
    }
}