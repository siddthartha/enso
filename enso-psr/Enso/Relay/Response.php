<?php declare(strict_types=1);

/**
 * Class Enso\Response
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use GuzzleHttp\Psr7\BufferStream;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use HttpSoft\Message\ResponseTrait;
use Swoole\Http\Response as SwooleResponse;

/**
 * Description of Response
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 *
 * @property float $before  microtime
 * @property float $after   microtime
 * @property string $preloadDuration    in ms
 * @property string $taskDuration       in ms
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
        if ($response instanceof Response)
        {
            $response = $response->collapse();
        }

        $_response->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
        $_response->header('Content-type', 'application/json');

        // SW emitting
        $_response->end(content: $response->getBody()->getContents());
    }

    /**
     * Apply Enso response data to PSR serialized body stream
     *
     * @return ResponseInterface
     */
    public function collapse(): ResponseInterface
    {
        if ((int) $this->getBody()->getSize() == 0)
        {
            // then serialize Response data from `$this->__attributes`
            $body = (new BufferStream());  // todo: find a place for this logic

            if ($body->write((string) $this))
            {
                return $this->withBody($body);
            }
        }

        return $this;
    }

    #[Pure]
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