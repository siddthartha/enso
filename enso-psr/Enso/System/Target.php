<?php
declare(strict_types = 1);
/**
 * Class Enso\System\Entry
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Enso;
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

    protected ?Enso $_context;

    /**
     *
     * @param string $className
     * @param string|array $methods
     */
    public function __construct(string $className, string|array $methods = [], ?Enso $context = null)
    {
        $this->_className = $className;
        $this->_methods = is_array($methods)
            ? $methods
            : [$methods];

        if (!class_exists($className, true))
        {
            throw new \BadFunctionCallException("No such class to instantiate action runner.");
        }

        $this->_context = $context;
    }

    /**
     *
     * @return object
     */
    public function getInstance(): object
    {
        $className = $this->_className;

        return new $className($this->_context);
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->_methods;
    }

}