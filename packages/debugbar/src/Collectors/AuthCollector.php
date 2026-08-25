<?php

declare(strict_types=1);

namespace Switch\DebugBar\Collectors;

class AuthCollector extends AbstractCollector
{
    private ?array $userData = null;
    private bool $authenticated = false;
    private string $guard = 'web';

    public function getName(): string
    {
        return 'auth';
    }

    public function getTitle(): string
    {
        return 'Auth';
    }

    public function getIcon(): string
    {
        return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    }

    public function getBadge(): ?string
    {
        if ($this->authenticated) {
            $name = $this->userData['name'] ?? $this->userData['email'] ?? $this->userData['id'] ?? 'User';
            return (string) $name;
        }

        return 'Guest';
    }

    public function getBadgeColor(): string
    {
        return $this->authenticated ? 'success' : 'default';
    }

    public function setUser(?object $user, string $guard = 'web'): self
    {
        $this->guard = $guard;

        if ($user === null) {
            $this->authenticated = false;
            $this->userData = null;
            return $this;
        }

        $this->authenticated = true;
        $data = [
            'class' => get_class($user),
            'id' => method_exists($user, 'getAuthIdentifier') ? $user->getAuthIdentifier() : ($user->id ?? null),
            'email' => $user->email ?? null,
            'name' => $user->name ?? $user->username ?? null,
        ];

        if (method_exists($user, 'toArray')) {
            $data['attributes'] = $this->sanitizeValue($user->toArray());
        }

        $this->userData = $data;
        return $this;
    }

    public function collect(): array
    {
        // Auto-detect Switch Foundation Auth if not explicitly set
        if (!$this->authenticated && function_exists('auth')) {
            try {
                $auth = auth();
                if ($auth !== null && method_exists($auth, 'check') && $auth->check()) {
                    $user = $auth->user();
                    if ($user !== null) {
                        $this->setUser($user, method_exists($auth, 'getDefaultGuard') ? $auth->getDefaultGuard() : 'web');
                    }
                }
            } catch (\Throwable) {
                // Ignore
            }
        }

        return [
            'authenticated' => $this->authenticated,
            'guard' => $this->guard,
            'user' => $this->userData,
        ];
    }

    public function reset(): void
    {
        $this->userData = null;
        $this->authenticated = false;
        $this->guard = 'web';
    }
}
