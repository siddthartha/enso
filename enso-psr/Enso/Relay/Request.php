<?php declare(strict_types=1);

/**
 * Class Enso\Request
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use Psr\Http\Message\RequestInterface;
use HttpSoft\Message\RequestTrait;

/**
 * Description of Request
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
abstract class Request implements RequestInterface
{
    use RequestTrait;

    use Subject;

    /**
     *
     */
    public function __construct(array $data = [])
    {
        $this->__attributes = $data;
    }

    abstract public function getRoute(): array;
    abstract public function getOrigin(): RequestInterface;
}
