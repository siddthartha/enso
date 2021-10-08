<?php declare(strict_types=1);

/**
 * Class Enso\Response
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use \Enso\Subject;

/**
 * Description of Response
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Response
{
    use Subject;

    /**
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->__properties['body'] = $data;
    }

    /**
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->attributes['body']);
    }
}