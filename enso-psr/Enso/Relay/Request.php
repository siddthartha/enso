<?php declare(strict_types=1);

/**
 * Class Enso\Request
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Enso\Subject;
use Psr\Http\Message\RequestInterface as PSRRequestInterface;
use HttpSoft\Message\RequestTrait;

/**
 * Description of Request
 *
 * @property mixed $before
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
abstract class Request implements RequestInterface, PSRRequestInterface
{
    use RequestTrait;

    use Subject;

    /**
     *
     */
    public function __construct(array $data = [])
    {
        $this->__attributes = $data;

        $this->init();

    }

    public function getRoute(): array
    {
        return ['default', 'index'];
    }

    /**
     * Sanitizes and prepares a URI path for use as route segments
     */
    protected function preparePath(string $uriPath): array
    {
        $path = trim(preg_replace('#/+#', '/', filter_var(rawurldecode($uriPath), FILTER_SANITIZE_URL)), '/');
        $segments = array_filter(explode('/', $path), static fn($s) => $s !== '' && $s !== '.');

        return array_reduce(
            array: $segments,
            callback: static fn (array $acc, string $item) => ($item === '..') ? $acc : [...$acc, $item],
            initial: [],
        );
    }

    /**
     * @return array
     */
    public function getTarget(): mixed
    {
        return $this->getRoute();
    }
}
