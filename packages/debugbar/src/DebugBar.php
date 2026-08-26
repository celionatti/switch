<?php

declare(strict_types=1);

namespace Switch\DebugBar;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\DebugBar\Collectors\AuthCollector;
use Switch\DebugBar\Collectors\CacheCollector;
use Switch\DebugBar\Collectors\CollectorInterface;
use Switch\DebugBar\Collectors\ConfigCollector;
use Switch\DebugBar\Collectors\EventCollector;
use Switch\DebugBar\Collectors\HistoryCollector;
use Switch\DebugBar\Collectors\LogCollector;
use Switch\DebugBar\Collectors\MemoryCollector;
use Switch\DebugBar\Collectors\QueryCollector;
use Switch\DebugBar\Collectors\RequestCollector;
use Switch\DebugBar\Collectors\RouteCollector;
use Switch\DebugBar\Collectors\SecurityCollector;
use Switch\DebugBar\Collectors\SessionCollector;
use Switch\DebugBar\Collectors\TimeCollector;
use Switch\DebugBar\Collectors\ViewCollector;
use Switch\DebugBar\Renderer\HtmlRenderer;
use Switch\DebugBar\Renderer\JsonRenderer;
use Switch\DebugBar\Storage\MemoryStorage;
use Switch\DebugBar\Storage\StorageInterface;

class DebugBar
{
    private static ?self $instance = null;

    private bool $enabled = true;
    private string $requestId;
    private string $dataUrl = '/_debugbar/data';

    /**
     * @var array<string, CollectorInterface>
     */
    private array $collectors = [];

    private ?StorageInterface $storage = null;
    private HtmlRenderer $htmlRenderer;
    private JsonRenderer $jsonRenderer;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function setInstance(?self $instance): void
    {
        self::$instance = $instance;
    }

    public function __construct(?string $requestId = null)
    {
        $this->requestId = $requestId ?? substr(md5(uniqid((string) mt_rand(), true)), 0, 12);
        $this->htmlRenderer = new HtmlRenderer();
        $this->jsonRenderer = new JsonRenderer();
        $this->storage = new MemoryStorage();

        $this->registerDefaultCollectors();
    }

    public function enable(): self
    {
        $this->enabled = true;
        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function setRequestId(string $id): self
    {
        $this->requestId = $id;
        return $this;
    }

    public function setDataUrl(string $url): self
    {
        $this->dataUrl = $url;
        return $this;
    }

    public function getDataUrl(): string
    {
        return $this->dataUrl;
    }

    public function setStorage(StorageInterface $storage): self
    {
        $this->storage = $storage;
        return $this;
    }

    public function getStorage(): ?StorageInterface
    {
        return $this->storage;
    }

    public function addCollector(CollectorInterface $collector): self
    {
        $this->collectors[$collector->getName()] = $collector;
        return $this;
    }

    public function getCollector(string $name): ?CollectorInterface
    {
        return $this->collectors[$name] ?? null;
    }

    public function hasCollector(string $name): bool
    {
        return isset($this->collectors[$name]);
    }

    public function removeCollector(string $name): self
    {
        unset($this->collectors[$name]);
        return $this;
    }

    /**
     * @return array<string, CollectorInterface>
     */
    public function getCollectors(): array
    {
        return $this->collectors;
    }

    /**
     * Register core built-in collectors.
     */
    private function registerDefaultCollectors(): void
    {
        $this->addCollector(new TimeCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new QueryCollector());
        $this->addCollector(new RouteCollector());
        $this->addCollector(new ViewCollector());
        $this->addCollector(new LogCollector());
        $this->addCollector(new RequestCollector());
        $this->addCollector(new SessionCollector());
        $this->addCollector(new AuthCollector());
        $this->addCollector(new CacheCollector());
        $this->addCollector(new EventCollector());
        $this->addCollector(new ConfigCollector());
        $this->addCollector(new HistoryCollector($this->requestId));

        if (class_exists(\Switch\Foundation\Sentinel\Sentinel::class)) {
            $this->addCollector(new SecurityCollector());
        }

        $this->bootFrameworkListeners();
    }

    /**
     * Automatically wire listeners into database connection and view engine.
     */
    private function bootFrameworkListeners(): void
    {
        if (class_exists(\Switch\Database\Connection\Connection::class)) {
            \Switch\Database\Connection\Connection::listen(function (string $sql, array $bindings, float $timeMs, string $conn): void {
                if ($this->enabled && isset($this->collectors['queries']) && $this->collectors['queries'] instanceof QueryCollector) {
                    $this->collectors['queries']->addQuery($sql, $bindings, $timeMs, $conn);
                }
            });
        }

        if (class_exists(\Switch\View\Engine\ViewEngine::class)) {
            \Switch\View\Engine\ViewEngine::listen(function (string $view, string $path, float $renderTimeMs, array $data): void {
                if ($this->enabled && isset($this->collectors['views']) && $this->collectors['views'] instanceof ViewCollector) {
                    $this->collectors['views']->addView($view, $path, $renderTimeMs, $data);
                }
            });
        }
    }

    // --- Performance Timing Shortcuts ---

    public function startMeasure(string $name, ?string $label = null): self
    {
        if ($this->enabled && isset($this->collectors['time']) && $this->collectors['time'] instanceof TimeCollector) {
            $this->collectors['time']->startMeasure($name, $label);
        }
        return $this;
    }

    public function stopMeasure(string $name): self
    {
        if ($this->enabled && isset($this->collectors['time']) && $this->collectors['time'] instanceof TimeCollector) {
            $this->collectors['time']->stopMeasure($name);
        }
        return $this;
    }

    public function measure(string $label, callable $callback, ?string $name = null): mixed
    {
        if (!$this->enabled || !isset($this->collectors['time']) || !($this->collectors['time'] instanceof TimeCollector)) {
            return $callback();
        }

        return $this->collectors['time']->measure($label, $callback, $name);
    }

    // --- Messages / Logs Shortcuts ---

    public function addMessage(mixed $message, string $level = 'info'): self
    {
        if ($this->enabled && isset($this->collectors['logs']) && $this->collectors['logs'] instanceof LogCollector) {
            $this->collectors['logs']->addMessage($message, $level);
        }
        return $this;
    }

    public function debug(mixed $message): self
    {
        return $this->addMessage($message, 'debug');
    }

    public function info(mixed $message): self
    {
        return $this->addMessage($message, 'info');
    }

    public function warning(mixed $message): self
    {
        return $this->addMessage($message, 'warning');
    }

    public function error(mixed $message): self
    {
        return $this->addMessage($message, 'error');
    }

    // --- Data Collection & Rendering ---

    /**
     * Collect data from all active collectors.
     *
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        if (!$this->enabled) {
            return [];
        }

        // Sync history list before collecting
        if ($this->storage !== null && isset($this->collectors['history']) && $this->collectors['history'] instanceof HistoryCollector) {
            $this->collectors['history']->setRequests($this->storage->latest(15));
        }

        $data = [];
        foreach ($this->collectors as $name => $collector) {
            if ($collector->isAvailable()) {
                $data[$name] = $collector->collect();
            }
        }

        return $data;
    }

    /**
     * Render the DebugBar HTML markup, CSS, and JS.
     */
    public function render(): string
    {
        if (!$this->enabled) {
            return '';
        }

        $data = $this->collect();

        if ($this->storage !== null) {
            $this->storage->save($this->requestId, $data);
        }

        return $this->htmlRenderer->render($this->collectors, $data, $this->requestId, $this->dataUrl);
    }

    /**
     * Render debug data as structured JSON.
     */
    public function renderJson(): string
    {
        if (!$this->enabled) {
            return '{}';
        }

        $data = $this->collect();
        return $this->jsonRenderer->render($data, $this->requestId);
    }

    /**
     * Inject DebugBar markup into a PSR-7 Response right before </body>.
     */
    public function inject(ResponseInterface $response): ResponseInterface
    {
        if (!$this->enabled) {
            return $response;
        }

        $contentType = $response->getHeaderLine('Content-Type');

        // Always add debug header
        $response = $response->withHeader('X-Switch-Debug-Bar', $this->requestId);

        // Only inject HTML into HTML responses
        if (str_contains(strtolower($contentType), 'text/html') || empty($contentType)) {
            $body = (string) $response->getBody();
            $pos = strripos($body, '</body>');

            if ($pos !== false) {
                $debugbarHtml = $this->render();
                $newBody = substr($body, 0, $pos) . $debugbarHtml . substr($body, $pos);

                if (class_exists(\Switch\Http\Stream::class)) {
                    return $response->withBody(\Switch\Http\Stream::create($newBody));
                }

                $bodyStream = $response->getBody();
                if ($bodyStream->isWritable() && $bodyStream->isSeekable()) {
                    $bodyStream->rewind();
                    $bodyStream->write($newBody);
                    return $response;
                }
            }
        }

        return $response;
    }

    /**
     * Reset all collectors for new lifecycle or tests.
     */
    public function reset(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->reset();
        }
        $this->requestId = substr(md5(uniqid((string) mt_rand(), true)), 0, 12);
    }
}
