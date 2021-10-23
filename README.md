# The Enso -- PHP8 micro framework #

The main goal is to get some template for making fast and tiny REST *or* Asynchronous services on PHP8+:
* inside immutable docker containers
* with Swoole / RoadRunner for multi-threading
* with Redis for queue
* with WebSockets for web / mobile clients
* without local FS dependency (should put logs and files to some cloud storages only)
* using strict [PSR-compatible](https://www.php-fig.org/psr/) components (as widely as possible)

### Basics ###

On application layer project runs with:
* PHP8+
* Mixed runtime CLI / WEB / Daemon (Swoole | RoadRunner)
* Mysql / Postgresql for state storage
* Redis (AMPQ / MQTT)
* WebSockets support
* Xdebug 3

Framework's code should use:
* Yiisoft components:
  - PSR-11 DI Container
  - PSR-3 Logger
  - PSR-6 Cache
  - DB Layer / AR
* Other components for HTTP:
  - PSR-7/17 Messages / Factories (Httpsoft)
  - PSR-15 Middlewares (custom)
  - PSR-18 Client (Guzzle)
* Also:
  - Codeception E2E testing/coverage
  - Swagger

### How do I get set up? ###

```docker-compose up -d --build```

### How do I run tests? ###

```docker-compose exec php vendor/bin/codecept```
