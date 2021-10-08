<?php declare(strict_types=1);

namespace Enso;

/**
 * Статический класс Enso\Singles
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
class Singles
{
    /**
     * Stored singleton objects
     *
     * @var array
     */
    private static $__handles = [];

    /**
     * Turn off constructor
     */
    private function __construct()
    {
        ;
    }

    /**
     * Turn off cloning
     */
    private function __clone()
    {
        ;
    }

    /**
     * Store singleton object
     *
     * @param Object &$object
     */
    public static function storeSingle(&$object): void
    {
        if (!isset(static::$__handles[get_class($object)]))
        {
            static::$__handles[get_class($object)] = $object;
        }
    }

    public static function hasSingle(string $class_name): bool
    {
        return isset(static::$__handles[$class_name]);
    }

    /**
     * Get singleton object
     *
     * @param  string $class_name
     * @return Single
     */
    public static function getSingle($class_name)
    {
        if (isset(static::$__handles[$class_name]))
        {
            return static::$__handles[$class_name];
        }

        throw new \Exception('No instance');
    }
}