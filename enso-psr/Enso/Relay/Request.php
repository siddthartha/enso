<?php declare(strict_types=1);

/**
 * Class Enso\Request
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use HttpSoft\Message\Stream;
use HttpSoft\Message\Uri;
use Psr\Http\Message\RequestInterface;
use HttpSoft\Message\RequestTrait;

/**
 * Description of Request
 *
 * @property mixed $before
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
        $this->stream = new Stream();
        $this->uri = new Uri(''); // should we use '' on empty construction?
    }

//    abstract public function getPSR(): RequestInterface;

    public function getRoute(): array
    {
        return ['default', 'index'];
    }
}
