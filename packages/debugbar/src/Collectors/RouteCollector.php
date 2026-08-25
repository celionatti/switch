<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class RouteCollector extends AbstractCollector
{
    private ?string $name = null;
    private ?string $uri = null;
    private ?string $method = null;
    private ?string $action = null;
    private ?string $controller = null;
    private ?string $controllerMethod = null;
    private array $middleware = [];
    private array $parameters = [];
    private ?string $file = null;
    private ?int $line = null;

    public function getName(): string
    {
        return 'route';
    }

    public function getTitle(): string
    {
        return 'Route';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a4.5 4.5 0 0 0 0-9H5a4 4 0 0 1 0-8h11.5"/><circle cx="18" cy="5" r="3"/></svg>';
    }

    public function getBadge(): ?string
    {
        if ($this->uri === null && $this->action === null) {
            return null;
        }

        if ($this->controller !== null) {
            $short = class_exists($this->controller)
                ? (new \ReflectionClass($this->controller))->getShortName()
                : basename(str_replace('\\', '/', $this->controller));
            return $short . '@' . ($this->controllerMethod ?? 'index');
        }

        return $this->uri ?? $this->action;
    }

    public function setRouteData(
        ?string $uri = null,
        ?string $method = null,
        mixed $action = null,
        array $middleware = [],
        array $parameters = [],
        ?string $name = null
    ): self {
        $this->uri = $uri;
        $this->method = $method;
        $this->middleware = $middleware;
        $this->parameters = $parameters;
        $this->name = $name;

        if (is_string($action)) {
            $this->action = $action;
            if (str_contains($action, '@')) {
                [$ctrl, $mth] = explode('@', $action, 2);
                $this->controller = $ctrl;
                $this->controllerMethod = $mth;
            } elseif (str_contains($action, '::')) {
                [$ctrl, $mth] = explode('::', $action, 2);
                $this->controller = $ctrl;
                $this->controllerMethod = $mth;
            }
        } elseif (is_array($action) && count($action) === 2) {
            $this->controller = is_object($action[0]) ? get_class($action[0]) : (string) $action[0];
            $this->controllerMethod = (string) $action[1];
            $this->action = $this->controller . '@' . $this->controllerMethod;
        } elseif ($action instanceof \Closure) {
            $ref = new \ReflectionFunction($action);
            $this->action = 'Closure (' . basename((string) $ref->getFileName()) . ':' . $ref->getStartLine() . ')';
            $this->file = $ref->getFileName() ?: null;
            $this->line = $ref->getStartLine() ?: null;
        }

        return $this;
    }

    public function collect(): array
    {
        return [
            'name' => $this->name,
            'uri' => $this->uri,
            'method' => $this->method,
            'action' => $this->action,
            'controller' => $this->controller,
            'controller_method' => $this->controllerMethod,
            'middleware' => $this->middleware,
            'middleware_count' => count($this->middleware),
            'parameters' => $this->parameters,
            'file' => $this->file,
            'line' => $this->line,
        ];
    }

    public function reset(): void
    {
        $this->name = null;
        $this->uri = null;
        $this->method = null;
        $this->action = null;
        $this->controller = null;
        $this->controllerMethod = null;
        $this->middleware = [];
        $this->parameters = [];
        $this->file = null;
        $this->line = null;
    }
}
