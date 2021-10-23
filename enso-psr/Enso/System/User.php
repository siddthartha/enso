<?php declare(strict_types = 1);
/**
 * Class Enso\System\User
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use function \posix_getuid;
use function \posix_getpwuid;

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
        $this->__attributes = posix_getpwuid(posix_getuid());
    }

    public function getUid(): int
    {
        return posix_getuid();
    }

    public function setUid(int $uid): bool
    {
        return posix_setuid(user_id: $uid);
    }

    public function __toString(): string
    {
        return json_encode($this->__attributes);
    }
}