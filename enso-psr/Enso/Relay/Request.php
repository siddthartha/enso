<?php declare(strict_types=1);

/**
 * Class Enso\Request
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use HttpSoft\Message\Stream;
use HttpSoft\Message\Uri;
use Psr\Http\Message\RequestInterface as PSRRequestInterface;
use HttpSoft\Message\RequestTrait;

/**
 * Description of Request
 *
 * @property mixed $before
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
abstract class Request implements RequestInterface, PSRRequestInterface
{
    use RequestTrait;

    use Subject;

    /**
     *
     */
    public function __construct(array $data = [])
    {
        $this->__attributes = $data;

        $this->init(
            body: new Stream(),
            uri: new Uri(),
        );

    }

    public function getRoute(): array
    {
        return ['default', 'index'];
    }

    /**
     * @return array
     */
    public function getTarget(): mixed
    {
        return $this->getRoute();
    }
}
