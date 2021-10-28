<?php declare(strict_types=1);

/**
 * Class Enso\Response
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use GuzzleHttp\Psr7\BufferStream;
use Psr\Http\Message\ResponseInterface;
use HttpSoft\Message\ResponseTrait;
use Swoole\Http\Response as SwooleResponse;

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
     * @param array $data
     * @param int $statusCode
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

    public static function toSwooleResponse(ResponseInterface $response, SwooleResponse $_response)
    {
        if ((int) $response->getBody()->getSize() == 0 && $response instanceof Response)
        {
            // then emit Response data
            $body = (new BufferStream());

            if ($body->write((string) $response))
            {
                $response = $response->withBody($body);
            }
        }

        $_response->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
        $_response->header('Content-type', 'application/json');
        $_response->end(content: $response->getBody()->getContents());
    }

    public function isError(): bool
    {
        return ($this->getStatusCode() >= 400);
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