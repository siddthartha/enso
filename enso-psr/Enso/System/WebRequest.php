<?php
declare(strict_types = 1);
/**
 * Class Enso\System\WebRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\Request;
use HttpSoft\Message\RequestTrait;
use HttpSoft\Message\StreamFactory;
use HttpSoft\Message\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleRequest;
use Yiisoft\Http\Method;
use GuzzleHttp\Psr7\
    {BufferStream, CachingStream, LazyOpenStream, ServerRequest};

use mb_ereg_replace;
use count;
use trim;
use explode;
use getallheaders;

/**
 * Description of WebRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 *
 * @property array|mixed $serverParams
 * @property array|mixed $parsedBody
 * @property array|mixed $cookies
 * @property array|mixed $queryParams
 * @property array|mixed $files
 *
 */
class WebRequest extends Request
{
    use RequestTrait;

    public function __construct(array $data = [], ?ServerRequestInterface $psr = null)
    {
        parent::__construct($data);

        if ($psr instanceof RequestInterface)
        {
            $this->init($psr->getMethod(),
                uri: $psr->getUri(),
                headers: $psr->getHeaders(),
                body: $psr->getBody(),
                protocol: $psr->getProtocolVersion()
            );
        }

        if ($psr instanceof ServerRequestInterface)
        {
            $this->files = ServerRequest::normalizeFiles($_FILES);
            $this->parsedBody = $_POST;
            $this->queryParams = $_GET;
            $this->cookies = $_COOKIE;
            $this->serverParams = $_SERVER;
        }
    }

    /**
     * Return a ServerRequest populated with superglobals:
     * $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER
     */
    public static function fromGlobals(): Request
    {
        $protocol = isset($_SERVER['SERVER_PROTOCOL'])
            ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL'])
            : '1.1';

        $request = new static();
        $request->init(
            method: $method = $_SERVER['REQUEST_METHOD'] ?? Method::GET,
            uri: $uri = ServerRequest::getUriFromGlobals(),
            headers: $headers = getallheaders(),
            body: $body = new CachingStream(new LazyOpenStream('php://input', 'r+')),
            protocol: $protocol
        );

        $request->files = ServerRequest::normalizeFiles($_FILES);
        $request->parsedBody = $_POST;
        $request->queryParams = $_GET;
        $request->cookies = $_COOKIE;
        $request->serverParams = $_SERVER;

        return $request;
    }

    /**
     * @param SwooleRequest $swooleRequest
     * @return Request
     */
    public static function fromSwooleRequest(SwooleRequest $swooleRequest): Request
    {
        $request = new static();

        $request->init(
            method: $method = $swooleRequest->getMethod(),
            uri: $uri = new Uri($swooleRequest->server['request_uri']),
            headers: $headers = $swooleRequest->header,
            body: $body = (new StreamFactory)->createStream((string) $swooleRequest->rawContent()),
            protocol: $protocol = '1.1'
        );

        $request->files = ServerRequest::normalizeFiles($swooleRequest->files ?? []);
        $request->parsedBody = $swooleRequest->post ?? [];
        $request->queryParams = $swooleRequest->get ?? [];
        $request->cookies = $swooleRequest->cookie ?? [];
        $request->serverParams = $_SERVER;

        return $request;
    }

    /**
     * @return ServerRequestInterface
     */
    public function asPsrServerRequest(): ServerRequestInterface
    {
        return (new ServerRequest(
            method: $this->method,
            uri: $this->uri,
            headers: $this->headers,
            body: $this->stream,
            version: $this->protocol,
            serverParams: $this->serverParams
        ))
        ->withCookieParams($this->cookies)
        ->withQueryParams($this->queryParams)
        ->withParsedBody($this->parsedBody)
        ->withUploadedFiles($this->files);
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        $phpSelfPath = $_SERVER['PHP_SELF'];

        $uriTarget = explode(
            '/',
            trim(
                mb_ereg_replace("^($phpSelfPath)", '', $this->getUri()->getPath()),
                '/'
            )
        );

        return count($uriTarget) == 0 || (count($uriTarget) == 1 && $uriTarget[0] == '')
            ? parent::getRoute()
            : $uriTarget;
    }
}