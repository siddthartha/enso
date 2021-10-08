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

    /**
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->__properties['body'] = $data;
    }
}