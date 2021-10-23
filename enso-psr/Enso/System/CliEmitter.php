<?php
declare(strict_types = 1);
/**
 * Class Enso\System\CliEmitter
 * copied from Yiisoft\Yii\Web\SapiEmitter
 * {@see https://github.com/yiisoft/yii-web/blob/master/src/SapiEmitter.php}
 */

namespace Enso\System;

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
 * CliEmitter sends a response body to stdout
 */
final class CliEmitter
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
     * @param ResponseInterface $response Response object to send.
     *
     * @throws \Exception
     */
    public function emit(ResponseInterface $response, bool $terminateAfter = false): void
    {
        $status = $response->getStatusCode();

        $this->emitBody($response);

        if ($terminateAfter)
        {
            exit ($status < 400 ? 0 : -1);
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

