<?php
declare(strict_types = 1);
/**
 * Class Enso\System\CliRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use HttpSoft\Message\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Description of CliRequest
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class CliRequest extends \Enso\Relay\Request
{
    private array $_arguments;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->_arguments = $_SERVER['argv'];
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        return isset($this->_arguments[1])
            ? explode('/', $this->_arguments[1])
            : parent::getRoute();
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->_arguments;
    }
}