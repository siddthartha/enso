<?php declare(strict_types=1);

/**
 * Class Enso\Response
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use GuzzleHttp\Psr7\BufferStream;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface as PSRResponseInterface;
use HttpSoft\Message\ResponseTrait;
use Psr\Http\Message\StreamInterface;
use Swoole\Http\Response as SwooleResponse;

/**
 * Description of Response
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 *
 */
class Response implements PSRResponseInterface, ResponseInterface
{
    use ResponseTrait;
    use Subject;

    private bool $_isStream;
    private mixed $payload;

    /**
     * @param array|StreamInterface $data
     * @param int $statusCode
     */
    public function __construct(array|StreamInterface $data = [], int $statusCode = 200)
    {
        $this->_isStream = $data instanceof StreamInterface;
        $this->__attributes = $this->_isStream ? null : $data;

        $this->init(
            statusCode: $statusCode,
            headers: [],
            body: $this->_isStream ? $data : null,
        );
    }

    public static function toSwooleResponse(PSRResponseInterface $response, SwooleResponse &$_response): SwooleResponse
    {
        $_response->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
        $_response->header = $response->getHeaders();

        $_response->write(
            data: $response->getBody()->getContents()
        );

        return $_response;
    }

    /**
     * If body stream's resource has a zero size OR it is unknown
     *
     * @return bool
     */
    public function isBodyEmpty(): bool
    {
        return ($this->getBody()->getSize() === 0
            || $this->getBody()->getSize() === null
        );
    }

    /**
     * Apply Enso response data to PSR serialized body stream
     *
     * @param bool $force
     * @return PSRResponseInterface
     */
    public function collapse(bool $force = false): PSRResponseInterface
    {
        if (!$this->isStream() || $force)
        {
            $body = (new BufferStream());

            // then serialize Response data from `$this->__attributes` using `__toString()`
            $isFull = $body->write((string) $this) == 0;

            if ($isFull)
            {
                ; // TODO: work with buffer
            }

            return $this->withBody($body);
        }

        return $this;
    }

    #[Pure]
    public function isError(): bool
    {
        return ($this->getStatusCode() >= 400);
    }

    /**
     * Some description
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->attributes);
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function withPayload($payload): ResponseInterface
    {
        $new = clone $this;
        $new->payload = $payload;

        return $new;
    }

    #[Pure]
    public function getStatus(): int
    {
        return $this->getStatusCode();
    }

    /**
     * @return bool
     */
    public function isStream(): bool
    {
        return $this->_isStream;
    }
}