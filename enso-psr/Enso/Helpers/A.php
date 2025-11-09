<?php
declare(strict_types = 1);
/**
 * Class Enso\Helpers\A
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Helpers;

use Yiisoft\Arrays\ArrayHelper;
use InvalidArgumentException;

/**
 * Description of A
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class A
{
    /**
     * Picks a random value from a given array
     *
     * @param array $array
     * @return mixed
     */
    public function random(array $array): mixed
    {
        if (empty($array)) {
            throw new InvalidArgumentException('Cannot pick a random element from an empty array');
        }

        return (static fn(&$_array) => $_array[array_rand($_array)])($array);
    }

    /**
     *
     * @param array $actualStateArray
     * @param array $inputStateArray
     * @return array
     */
    public static function completion(array $actualStateArray, array $inputStateArray): array
    {
        $addedValuesArray   = array_diff_assoc($inputStateArray, $actualStateArray);
        $removedValuesArray = array_diff_assoc($actualStateArray, $inputStateArray);

        return [$addedValuesArray, $removedValuesArray];
    }

    public static function get(array &$array, array|\Closure|float|int|string $path, $default = null): mixed
    {
        return ArrayHelper::getValue($array, $path, $default);
    }

    public static function merge(...$arrays): array
    {
        return ArrayHelper::merge(...$arrays);
    }

    /**
     * Proxy calls for Yiisoft ArrayHelper methods
     *
     * @param string $name
     * @param array $arguments
     * @return false|mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return forward_static_call([ArrayHelper::class, $name], ...$arguments);
    }
}