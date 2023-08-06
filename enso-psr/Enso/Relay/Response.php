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

    private mixed $payload;

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

    public static function toSwooleResponse(PSRResponseInterface $response, SwooleResponse &$_response): SwooleResponse
    {
        $_response->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
        $_response->header = $response->getHeaders();

        $_response->write(
            content: $response->getBody()->getContents()
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
        return ($this->getBody()->getSize() == 0
            || $this->getBody()->getSize() == null
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
        if ($force || $this->isBodyEmpty())
        {
            // then serialize Response data from `$this->__attributes`
            $body = (new BufferStream());

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
}