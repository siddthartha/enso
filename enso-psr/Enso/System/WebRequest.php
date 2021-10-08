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
        $this->uri = explode('?', $_SERVER['REQUEST_URI']);
    }

}