<?php
declare(strict_types = 1);
/**
 * Class Enso\System\Entry
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use function class_exists;

/**
 * Description of Entry
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Entry
{
    protected $_className;

    /**
     *
     * @param string $className
     * @throws \BadFunctionCallException
     */
    public function __construct(string $className)
    {
        $this->_className = $className;

        if (!class_exists($className, true))
        {
            throw new \BadFunctionCallException("No such class to instantiate action runner.");
        }
    }

    /**
     *
     * @return object
     */
    public function getInstance(): object
    {
        $className = $this->_className;

        return new $className();
    }
}