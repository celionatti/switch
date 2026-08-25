<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestCollector extends AbstractCollector
{
    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;

    public function getName(): string
    {
        return 'request';
    }

    public function getTitle(): string
    {
        return 'Request';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    }

    public function getBadge(): ?string
    {
        if ($this->response !== null) {
            return (string) $this->response->getStatusCode();
        }

        if ($this->request !== null) {
            return $this->request->getMethod();
        }

        return null;
    }

    public function getBadgeColor(): string
    {
        if ($this->response !== null) {
            $code = $this->response->getStatusCode();
            if ($code >= 500) {
                return 'danger';
            }
            if ($code >= 400) {
                return 'warning';
            }
            if ($code >= 300) {
                return 'info';
            }
            return 'success';
        }

        return 'default';
    }

    public function setRequest(ServerRequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function collect(): array
    {
        $reqData = [];
        $resData = [];

        if ($this->request !== null) {
            $uri = $this->request->getUri();
            $server = $this->request->getServerParams();
            $queryParams = $this->request->getQueryParams();
            $parsedBody = $this->request->getParsedBody();
            $headers = $this->request->getHeaders();
            $cookies = $this->request->getCookieParams();

            // Mask sensitive headers
            $sanitizedHeaders = [];
            foreach ($headers as $name => $values) {
                $val = implode(', ', $values);
                if (preg_match('/(authorization|token|key|secret|password)/i', $name)) {
                    $val = substr($val, 0, 8) . '…[MASKED]';
                }
                $sanitizedHeaders[$name] = $val;
            }

            $reqData = [
                'method' => $this->request->getMethod(),
                'uri' => (string) $uri,
                'path' => $uri->getPath() ?: '/',
                'query_string' => $uri->getQuery(),
                'query_params' => $queryParams,
                'body' => $this->sanitizeValue($parsedBody),
                'headers' => $sanitizedHeaders,
                'cookies' => $this->sanitizeValue($cookies),
                'ip' => $server['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $server['HTTP_USER_AGENT'] ?? null,
                'is_ajax' => strtolower($server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
                    || isset($server['HTTP_HX_REQUEST'])
                    || isset($server['HTTP_X_LIVE_REQUEST']),
            ];
        }

        if ($this->response !== null) {
            $headers = $this->response->getHeaders();
            $flatHeaders = [];
            foreach ($headers as $name => $values) {
                $flatHeaders[$name] = implode(', ', $values);
            }

            $resData = [
                'status_code' => $this->response->getStatusCode(),
                'reason_phrase' => $this->response->getReasonPhrase(),
                'protocol_version' => $this->response->getProtocolVersion(),
                'headers' => $flatHeaders,
            ];
        }

        return [
            'request' => $reqData,
            'response' => $resData,
        ];
    }

    public function reset(): void
    {
        $this->request = null;
        $this->response = null;
    }
}
