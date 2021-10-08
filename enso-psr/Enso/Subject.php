<?php declare(strict_types=1);

namespace Enso;

/**
 * Класс Subject
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
trait Subject
{
    /**
     * All the properties storage
     *
     * @var array
     */
    protected $__properties = [];

    /**
     * Magic getter
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property): mixed
    {
        if (method_exists($this, '__get_' . $property))
        {
            return $this->{'__get_' . $property}();
        }

        if (isset($this->__properties[$property]))
        {
            return $this->__properties[$property];
        }

        throw new \BadMethodCallException();
    }

    /**
     * Magic setter
     *
     * @param string $property
     * @param mixed $value
     * @return mixed
     */
    public function __set(string $property, $value): void
    {
        if (method_exists($this, '__set_' . $property))
        {
            $this->{'__set_' . $property}($value);

            return ;
        }

        $this->__properties[$property] = $value;
    }

    public function __get_attributes(): array
    {
        return $this->__properties;
    }
}