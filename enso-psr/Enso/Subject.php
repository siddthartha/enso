<?php declare(strict_types=1);

namespace Enso;

use Enso\Helpers\Internal;

/**
 * Класс Subject
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
trait Subject
{
//    public const MAGIC_GETTER_PREFIX = '__get_';
//    public const MAGIC_SETTER_PREFIX = '__set_';

    /**
     * All the properties storage
     *
     * @var array
     */
    protected $__attributes = [];

    /**
     * Magic getter
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get(string $attribute): mixed
    {
        if (method_exists($this, Internal::MAGIC_GETTER_PREFIX . $attribute))
        {
            return $this->{Internal::MAGIC_GETTER_PREFIX . $attribute}();
        }

        if (isset($this->__attributes[$attribute]))
        {
            return $this->__attributes[$attribute];
        }

        throw new \BadMethodCallException("Attribute $attribute not found.");
    }

    /**
     * Magic setter
     *
     * @param string $attribute
     * @param mixed $value
     * @return mixed
     */
    public function __set(string $attribute, $value): void
    {
        if (method_exists($this, Internal::MAGIC_SETTER_PREFIX . $attribute))
        {
            $this->{Internal::MAGIC_SETTER_PREFIX . $attribute}($value);

            return;
        }

        $this->__attributes[$attribute] = $value;
    }

    /**
     *
     * @return array
     */
    public function __get_attributes(): array
    {
        return $this->__attributes;
    }
}