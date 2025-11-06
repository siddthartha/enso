<?php
/**
 * PhpStorm stub file to map Swoole classes to OpenSwoole for better autocomplete
 * This file helps PhpStorm understand that Swoole classes have OpenSwoole methods/properties
 */

namespace Swoole\Http {
    /**
     * @extends \OpenSwoole\Http\Server
     */
    class Server extends \OpenSwoole\Http\Server {}

    /**
     * @extends \OpenSwoole\Http\Request
     */
    class Request extends \OpenSwoole\Http\Request {}

    /**
     * @extends \OpenSwoole\Http\Response
     */
    class Response extends \OpenSwoole\Http\Response {}
}

namespace Swoole {
    /**
     * @extends \OpenSwoole\Coroutine
     */
    class Coroutine extends \OpenSwoole\Coroutine {}
}
