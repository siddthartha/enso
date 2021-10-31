<?php
declare(strict_types = 1);
/**
 * Class Enso\System\WebRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\Request;
use HttpSoft\Message\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use GuzzleHttp\Psr7\
    {BufferStream, CachingStream, LazyOpenStream, ServerRequest};

use mb_ereg_replace;
use count;
use trim;
use explode;

/**
 * Description of WebRequest
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class WebRequest extends Request
{
    private ?ServerRequestInterface $_requestOrigin = null;

    public function __construct(array $data = [], ?ServerRequestInterface $psr = null)
    {
        parent::__construct($data);
        $this->_requestOrigin = $psr;
    }

    /**
     * Return a ServerRequest populated with superglobals:
     * $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER
     */
    public static function fromGlobals(): Request
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? Method::GET;

        $headers = getallheaders();

        $uri = ServerRequest::getUriFromGlobals();

        $body = new CachingStream(
            new LazyOpenStream('php://input', 'r+')
        );

        $protocol = isset($_SERVER['SERVER_PROTOCOL'])
            ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL'])
            : '1.1';

        $request = new static();

        $request->_requestOrigin =
            (new ServerRequest($method, $uri, $headers, $body, $protocol, $_SERVER))
                ->withCookieParams($_COOKIE)
                ->withQueryParams($_GET)
                ->withParsedBody($_POST)
                ->withUploadedFiles(ServerRequest::normalizeFiles($_FILES));

        return $request;
    }

    public static function fromSwooleRequest(\Swoole\Http\Request $swooleRequest): Request
    {
        $method = $swooleRequest->getMethod();

        $headers = $swooleRequest->header;

        $uri = new Uri($swooleRequest->server['request_uri']);

        $body = (new BufferStream());
        $body->write((string) $swooleRequest->rawContent());

        $protocol = '1.1'; // @TODO: protocol detection

        $request = new static();

        $request->_requestOrigin =
            (new ServerRequest($method, $uri, $headers ?? [], $body, $protocol, $_SERVER))
                ->withCookieParams($swooleRequest->cookie ?? [])
                ->withQueryParams($swooleRequest->get ?? [])
                ->withParsedBody($swooleRequest->post ?? [])
                ->withUploadedFiles(ServerRequest::normalizeFiles($swooleRequest->files ?? []));

        $request->swooleRequestUri = $swooleRequest->server['request_uri'];

        return $request;
    }


    /**
     *
     * @return ServerRequestInterface
     */
    public function getPSR(): ServerRequestInterface
    {
        return $this->_requestOrigin
            ?? ($this->_requestOrigin = new ServerRequest(Method::GET, ""));
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
                mb_ereg_replace("^($phpSelfPath)", '', $this->getPSR()->getUri()->getPath()),
                '/'
            )
        );

        return count($uriTarget) == 0 || (count($uriTarget) == 1 && $uriTarget[0] == "")
            ? parent::getRoute()
            : $uriTarget;
    }
}