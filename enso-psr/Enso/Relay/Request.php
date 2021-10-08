<?php declare(strict_types=1);

/**
 * Class Enso\Request
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use \Enso\Subject;

/**
 * Description of Request
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Request
{
    use Subject;

    public function __construct(array $data)
    {
        $this->__properties['body'] = $data;
    }

    public function __toString(): string
    {
        return json_encode($this->__properties['body']);
    }
}