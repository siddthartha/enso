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

        [ $uri, $query ] = explode('?', $_SERVER['REQUEST_URI'] . "?");

        $this->input = [
            'headers' => apache_request_headers(),
            'method' => $_SERVER['REQUEST_METHOD'],
            'post' => $_POST,
            'get' => $_GET,
            'files' => $_FILES,
            'uri' => $uri,
            'query' => $query,
        ];
    }

    /**
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        $path = explode('/', trim($this->input['uri'], " \t\n\r\0\x0B\/"));

        return count($path) == 1 && $path[0] == ""
            ? ['default', 'action']
            : $path;
    }
}