<?php declare(strict_types=1);

/**
 * Class Enso\Response
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use Psr\Http\Message\ResponseInterface;
use HttpSoft\Message\ResponseTrait;

/**
 * Description of Response
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Response implements ResponseInterface
{
    use ResponseTrait;
    use Subject;

    /**
     *
     * @param array $data
     */
    public function __construct(array $data = [], int $statusCode = 200)
    {
        $this->__attributes = $data;

        $headers = [];
        $body = null;
        $protocol = '1.1';
        $reasonPhrase = '';

        $this->init($statusCode, $reasonPhrase, $headers, $body, $protocol);

    }

    /**
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->attributes);
    }
}