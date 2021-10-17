<?php
declare(strict_types = 1);
/**
 * Class Enso\System\CliRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

/**
 * Description of CliRequest
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class CliRequest extends \Enso\Relay\Request
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->arguments = $_SERVER['argv'];
        $this->route = $this->getRoute();
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        return isset($this->arguments[1])
            ? explode('/', $this->arguments[1])
            : ['default', 'action'];
    }

}