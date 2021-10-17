<?php declare(strict_types=1);

/**
 * Class Enso\Request
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\ServerRequest;

use HttpSoft\Message\RequestTrait;
use Yiisoft\Http\Method;

/**
 * Description of Request
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Request implements RequestInterface
{
    use RequestTrait;

    use Subject;

    private $_requestOrigin;

    /**
     *
     */
    public function __construct(array $data = [])
    {
        $this->__attributes = $data;
    }

    /**
     * Return a ServerRequest populated with superglobals:
     * $_GET
     * $_POST
     * $_COOKIE
     * $_FILES
     * $_SERVER
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
    public function getOrigin(): ServerRequestInterface
    {
        return $this->_requestOrigin;
    }
}
