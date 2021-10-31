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
class Target
{
    protected string $_className;

    protected string | array $_methods;

    /**
     *
     * @param string $className
     * @param string|array $methods
     */
    public function __construct(string $className, string|array $methods = [])
    {
        $this->_className = $className;
        $this->_methods = is_array($methods)
            ? $methods
            : [$methods];

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

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->_methods;
    }

}