<?php
declare(strict_types = 1);
/**
 * Class Enso\System\WebRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\Request;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use GuzzleHttp\Psr7\
    {CachingStream, LazyOpenStream, ServerRequest};

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
    private ServerRequestInterface $_requestOrigin;

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

    /**
     *
     * @return ServerRequestInterface
     */
    public function getPSR(): ServerRequestInterface
    {
        return $this->_requestOrigin;
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        $phpSelfPath = $_SERVER['PHP_SELF'];

        $uriPath = explode(
            '/',
            trim(
                mb_ereg_replace("^($phpSelfPath)", '', $this->getPSR()->getUri()->getPath()),
                '/'
            )
        );

        return count($uriPath) == 0 || (count($uriPath) == 1 && $uriPath[0] == "")
            ? parent::getRoute()
            : $uriPath;
    }
}