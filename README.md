# PHP8 micro framework #

The main goal is to get some template for making fast and tiny REST or Asynchronous services on PHP8:
* inside immutable docker containers
* with Swoole for multi-threading
* with Redis for queue
* with WebSockets for web/mobile clients
* without local FS dependency (should put logs and files to some cloud storages only)
* using [PSR-compatible](https://www.php-fig.org/psr/) components

### Basics ###

Immutable docker containers with:
* PHP8
* Swoole
* XDebug 3
* Mysql/Postgresql
* Redis
* Websockets

Framework code using:
* Yiisoft components:
  - PSR-11 DI Container
  - PSR-3 Logger
  - PSR-6 Cache
* Guzzle components for HTTP:
  - PSR-7/17 Messages/Factories
  - PSR-15 Middlewares
  - PSR-18 Client


### How do I get set up? ###

```docker-compose up -d --build```
