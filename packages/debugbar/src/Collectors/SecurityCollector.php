<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Foundation\Sentinel\DiagnosticResult;
use Switch\Foundation\Sentinel\QueryAnalyzer;
use Switch\Foundation\Sentinel\SecurityScanner;
use Switch\Foundation\Sentinel\Sentinel;

class SecurityCollector extends AbstractCollector
{
    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $queries = [];

    public function getName(): string
    {
        return 'security';
    }

    public function getTitle(): string
    {
        return 'Security';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
    }

    public function setRequestResponse(?ServerRequestInterface $request, ?ResponseInterface $response = null): self
    {
        $this->request = $request;
        $this->response = $response;
        return $this;
    }

    public function setQueries(array $queries): self
    {
        $this->queries = $queries;
        return $this;
    }

    public function getBadge(): ?string
    {
        $data = $this->collect();
        $critical = $data['counts']['critical'] ?? 0;
        $warning = $data['counts']['warning'] ?? 0;

        if ($critical > 0) {
            return "{$critical} Critical";
        }

        if ($warning > 0) {
            return "{$warning} Alerts";
        }

        return '100%';
    }

    public function getBadgeColor(): string
    {
        $data = $this->collect();
        $critical = $data['counts']['critical'] ?? 0;
        $warning = $data['counts']['warning'] ?? 0;

        if ($critical > 0) {
            return 'danger';
        }

        if ($warning > 0) {
            return 'warning';
        }

        return 'success';
    }

    public function collect(): array
    {
        $results = [];

        // 1. Runtime Request/Response Inspection
        if ($this->request !== null) {
            $scanner = new SecurityScanner();
            $results = array_merge($results, $scanner->inspectRuntime($this->request, $this->response));
        }

        // 2. Query Vulnerability Analysis
        if (!empty($this->queries)) {
            $queryAnalyzer = new QueryAnalyzer();
            $results = array_merge($results, $queryAnalyzer->analyze($this->queries));
        }

        // 3. Static Config Check
        $scanner = new SecurityScanner();
        $results = array_merge($results, $scanner->auditConfig());

        $counts = [
            'critical' => 0,
            'warning' => 0,
            'info' => 0,
            'pass' => 0,
        ];

        $penalty = 0;
        $formattedResults = [];

        foreach ($results as $res) {
            if ($res instanceof DiagnosticResult) {
                if ($res->isCritical()) {
                    $counts['critical']++;
                    $penalty += 25;
                } elseif ($res->isWarning()) {
                    $counts['warning']++;
                    $penalty += 10;
                } elseif ($res->level === DiagnosticResult::LEVEL_INFO) {
                    $counts['info']++;
                    $penalty += 2;
                } else {
                    $counts['pass']++;
                }

                $formattedResults[] = $res->toArray();
            }
        }

        $score = max(0, min(100, 100 - $penalty));
        $grade = match (true) {
            $score >= 95 => 'A+',
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F',
        };

        return [
            'score' => $score,
            'grade' => $grade,
            'is_healthy' => $counts['critical'] === 0,
            'counts' => $counts,
            'results' => $formattedResults,
        ];
    }

    public function reset(): void
    {
        $this->request = null;
        $this->response = null;
        $this->queries = [];
    }
}
