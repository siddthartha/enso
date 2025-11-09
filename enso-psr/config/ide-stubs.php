<?php
/**
 * PhpStorm stub file to map Swoole classes to OpenSwoole for better autocomplete
 * Based on actual usage in Enso Framework and OpenSwoole IDE Helper
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

    /**
     * @extends \OpenSwoole\Timer
     */
    class Timer extends \OpenSwoole\Timer {}
    
    /**
     * @extends \OpenSwoole\Process
     */
    class Process extends \OpenSwoole\Process {}
    
    /**
     * @extends \OpenSwoole\Atomic
     */
    class Atomic extends \OpenSwoole\Atomic {}
    
    /**
     * @extends \OpenSwoole\Table
     */
    class Table extends \OpenSwoole\Table {}
    
    /**
     * @extends \OpenSwoole\Lock
     */
    class Lock extends \OpenSwoole\Lock {}
    
    /**
     * @extends \OpenSwoole\Server
     */
    class Server extends \OpenSwoole\Server {}
    
    /**
     * @extends \OpenSwoole\Client
     */
    class Client extends \OpenSwoole\Client {}
    
    /**
     * @extends \OpenSwoole\Util
     */
    class Util extends \OpenSwoole\Util {}
    
    /**
     * @extends \OpenSwoole\Exception
     */
    class Exception extends \OpenSwoole\Exception {}
}

namespace Swoole\Coroutine {
    /**
     * @extends \OpenSwoole\Coroutine\Socket
     */
    class Socket extends \OpenSwoole\Coroutine\Socket {}
    
    /**
     * @extends \OpenSwoole\Coroutine\Context
     */
    class Context extends \OpenSwoole\Coroutine\Context {}
    
    /**
     * @extends \OpenSwoole\Coroutine\Client
     */
    class Client extends \OpenSwoole\Coroutine\Client {}
    
    /**
     * @extends \OpenSwoole\Coroutine\PostgreSQL
     */
    class PostgreSQL extends \OpenSwoole\Coroutine\PostgreSQL {}
    
    /**
     * @extends \OpenSwoole\Coroutine\Scheduler
     */
    class Scheduler extends \OpenSwoole\Coroutine\Scheduler {}
    
    /**
     * @extends \OpenSwoole\Coroutine\Iterator
     */
    class Iterator extends \OpenSwoole\Coroutine\Iterator {}
    
    /**
     * @extends \OpenSwoole\Coroutine\Channel
     */
    class Channel extends \OpenSwoole\Coroutine\Channel {}
}

namespace Swoole\Coroutine\Http {
    /**
     * @extends \OpenSwoole\Coroutine\Http\Client
     */
    class Client extends \OpenSwoole\Coroutine\Http\Client {}
}

namespace Swoole\WebSocket {
    /**
     * @extends \OpenSwoole\WebSocket\Server
     */
    class Server extends \OpenSwoole\WebSocket\Server {}
    
    /**
     * @extends \OpenSwoole\WebSocket\Frame
     */
    class Frame extends \OpenSwoole\WebSocket\Frame {}
}

namespace Swoole\Http2 {
    /**
     * @extends \OpenSwoole\Http2\Request
     */
    class Request extends \OpenSwoole\Http2\Request {}
    
    /**
     * @extends \OpenSwoole\Http2\Response
     */
    class Response extends \OpenSwoole\Http2\Response {}
}
