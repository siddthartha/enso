<?php
declare(strict_types = 1);
/**
 * Class Enso\System\CliEmitter
 */

namespace Enso\System;

use Enso\Relay\EmitterInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Http\Status;
use Yiisoft\Http\Method;

use Enso\Helpers\Runtime;

use function flush;

/**
 * CliEmitter sends a response body to stdout
 */
final class CliEmitter implements EmitterInterface
{
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
     * Respond to the client with raw data to stdout (cli mode)
     *
     * @param ResponseInterface $response
     * @param bool $terminateAfter
     */
    public function emit(ResponseInterface $response, bool $terminateAfter = false): void
    {
        if (!Runtime::isDaemon())
        {
            $this->emitBody($response);
        }

        if ($terminateAfter)
        {
            $status = $response->getStatusCode();

            exit ($status < 400
                ? Runtime::EXIT_SUCCESS
                : Runtime::EXIT_FATAL
            );
        }
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
}

