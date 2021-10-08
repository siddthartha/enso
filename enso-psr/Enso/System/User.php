<?php declare(strict_types=1);
/**
 * Class Enso\System\User
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

/**
 * Description of User
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class User
{
    use \Enso\Subject;

    public function __construct()
    {
        $this->__properties = posix_getpwuid(posix_getuid());
    }

    public function __toString(): string
    {
        return json_encode($this->__properties);
    }
}