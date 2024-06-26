<?php
declare(strict_types = 1);
/**
 * Class Enso\System\WebEmitter
 * {@see https://github.com/yiisoft/yii-web/blob/master/src/SapiEmitter.php}
 */

namespace Enso\System;

use Enso\Relay\EmitterInterface;
use Enso\Relay\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Http\Status;
use Yiisoft\Http\Method;

use Enso\Helpers\Runtime;

use function flush;
use function in_array;
use function sprintf;
use function strtolower;

/**
 * SapiEmitter sends a response using standard PHP Server API i.e. with {@see header()} and "echo".
 */
final class WebEmitter implements EmitterInterface
{
    private const NO_BODY_RESPONSE_CODES = [
        Status::CONTINUE,
        Status::SWITCHING_PROTOCOLS,
        Status::PROCESSING,
        Status::NO_CONTENT,
        Status::RESET_CONTENT,
        Status::NOT_MODIFIED,
    ];

    private const DEFAULT_BUFFER_SIZE = 65535; // 64Kb

    private int $bufferSize;

    public function __construct(int $bufferSize = null)
    {
        if ($bufferSize !== null && $bufferSize <= 0)
        {
            throw new InvalidArgumentException('Buffer size must be greater than zero.');
        }
        $this->bufferSize = $bufferSize ?? self::DEFAULT_BUFFER_SIZE;
    }

    /**
     * Respond to the client with headers and body.
     *
     * @param ResponseInterface $response Response object to send.
     * @param bool $withoutBody If body should be ignored.
     *
     * @throws \Exception
     */
    public function emit(ResponseInterface $response, bool $withoutBody = false, bool $terminateAfter = false): void
    {
        $status = $response->getStatusCode();
        $withoutBody = $withoutBody || !$this->shouldOutputBody($response);
        $withoutContentLength = /* $withoutBody || */ $response->hasHeader('Transfer-Encoding');
        if ($withoutContentLength)
        {
            $response = $response->withoutHeader('Content-Length');
        }

        // We can't send headers if they are already sent.
        if (headers_sent() && !Runtime::isCLI())
        {
            throw new \Exception('Headers have been sent');
        }

        $this->clearHeaders();

        // Send HTTP Status-Line.
        $this->sendHeader(
            string: sprintf(
                'HTTP/%s %d %s',
                $response->getProtocolVersion(),
                $status,
                $response->getReasonPhrase()
            ),
            replace: true,
            code: $status
        );

        // Send headers.
        foreach ($response->getHeaders() as $header => $values)
        {
            $replaceFirst = strtolower($header) !== 'set-cookie';
            foreach ($values as $value)
            {
                $this->sendHeader("{$header}: {$value}", $replaceFirst);
                $replaceFirst = false;
            }
        }

        if (!$withoutContentLength && !$response->hasHeader('Content-Length'))
        {
            if (
                ($contentLength = $response->getBody()->getSize())
                !== null
            ) {
                $this->sendHeader("Content-Length: {$contentLength}", true);
            }
        }

        if ($withoutBody)
        {
            return;
        }

        $this->emitBody($response);
    }

    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();
        if ($body->isSeekable())
        {
            $body->rewind();
        }

        while (!$body->eof())
        {
            echo $body->read($this->bufferSize);
            flush();
        }
    }

    private function shouldOutputBody(ResponseInterface $response): bool
    {
        if (in_array($response->getStatusCode(), self::NO_BODY_RESPONSE_CODES, true))
        {
            return false;
        }

        // Check if body is empty.
        $body = $response->getBody();
        if (!$body->isReadable())
        {
            return false;
        }

        $size = $body->getSize();
        if ($size !== null)
        {
            return $size > 0;
        }

        if ($body->isSeekable())
        {
            $body->rewind();
            $byte = $body->read(1);

            if ($byte === '' || $body->eof())
            {
                return false;
            }
        }

        return true;
    }

    private function areHeaderSent(): bool
    {
        return headers_sent();
    }

    private function sendHeader(string $string, bool $replace = true, int $code = null): void
    {
        if (Runtime::isCLI())
        {
            return;
        }

        if ($code !== null)
        {
            header($string, $replace, $code);
        }
        else
        {
            header($string, $replace);
        }
    }

    private function clearHeaders(): void
    {
        if (Runtime::isCLI())
        {
            return;
        }

        header_remove();
    }
}
