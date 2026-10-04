<?php
declare(strict_types = 1);
/**
 * Class Enso\System\Target
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Enso;
use function class_exists;

/**
 * Description of Target
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Target
{
    protected string $_className;

    /** @var Method[] */
    protected array $_methods = [];

    /** @var Environment[] */
    protected array $_environments = [];

    protected ?Enso $_context;

    /**
     * @param string $className
     * @param Method|string|array<int, Method|string>|null $methods
     * @param Environment|string|array<int, Environment|string>|null $environments
     * @param Enso|null $context
     */
    public function __construct(
        string $className,
        Method|string|array|null $methods = null,
        Environment|string|array|null $environments = null,
        ?Enso &$context = null,
    ) {
        $this->_className = $className;

        if ($methods !== null) {
            $this->_methods = $this->normalizeMethods($methods);
        }

        if ($environments !== null) {
            $this->_environments = $this->normalizeEnvironments($environments);
        }

        if (!class_exists($className, true))
        {
            throw new \BadFunctionCallException("No such class to instantiate action runner.");
        }

        $this->_context = &$context;
    }

    /**
     * @param Method|string|array<int, Method|string> $methods
     * @return Method[]
     */
    protected function normalizeMethods(Method|string|array $methods): array
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        return array_map(
            fn ($m) => $m instanceof Method ? $m : Method::from(strtoupper($m)),
            $methods,
        );
    }

    /**
     * @param Environment|string|array<int, Environment|string> $environments
     * @return Environment[]
     */
    protected function normalizeEnvironments(Environment|string|array $environments): array
    {
        if (!is_array($environments)) {
            $environments = [$environments];
        }

        return array_map(
            fn ($e) => $e instanceof Environment ? $e : Environment::from(strtoupper($e)),
            $environments,
        );
    }

    /**
     * @return object
     */
    public function getInstance(): object
    {
        $className = $this->_className;

        return new $className($this->_context);
    }

    /**
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->_methods;
    }

    /**
     * @return Environment[]
     */
    public function getEnvironments(): array
    {
        return $this->_environments;
    }

    /**
     * Set the Enso context (called at runtime by entrypoint)
     */
    public function setContext(Enso &$context): void
    {
        $this->_context = &$context;
    }

    public function allowsMethod(Method $method): bool
    {
        return empty($this->_methods) || in_array($method, $this->_methods, true);
    }

    public function allowsEnvironment(Environment $env): bool
    {
        return empty($this->_environments) || in_array($env, $this->_environments, true);
    }
}