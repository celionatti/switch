<?php

declare(strict_types=1);

namespace Switch\View\Vite;

class ViteManifest
{
    private string $basePath;
    private string $buildDirectory;

    public function __construct(string $basePath, string $buildDirectory = 'build')
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->buildDirectory = trim($buildDirectory, '/\\');
    }

    /**
     * Generate HTML tags for Vite assets.
     *
     * @param string|array<int, string> $entrypoints
     */
    public function render(string|array $entrypoints): string
    {
        $entrypoints = is_array($entrypoints) ? $entrypoints : [$entrypoints];
        $hotFile = $this->basePath . '/public/hot';

        // 1. Vite Dev Server Mode (HMR)
        if (file_exists($hotFile)) {
            $devServerUrl = rtrim(trim((string) file_get_contents($hotFile)), '/');
            $tags = [];
            $tags[] = '<script type="module" src="' . htmlspecialchars($devServerUrl . '/@vite/client', ENT_QUOTES) . '"></script>';

            foreach ($entrypoints as $entry) {
                $url = $devServerUrl . '/' . ltrim($entry, '/');
                if (str_ends_with($entry, '.css') || str_ends_with($entry, '.scss')) {
                    $tags[] = '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES) . '">';
                } else {
                    $tags[] = '<script type="module" src="' . htmlspecialchars($url, ENT_QUOTES) . '"></script>';
                }
            }

            return implode("\n", $tags);
        }

        // 2. Production Manifest Mode
        $manifestPath = $this->basePath . '/public/' . $this->buildDirectory . '/manifest.json';
        if (!file_exists($manifestPath)) {
            $manifestPath = $this->basePath . '/public/' . $this->buildDirectory . '/.vite/manifest.json';
        }

        if (!file_exists($manifestPath)) {
            // Fallback: render direct link in development if neither hot nor manifest exists
            $tags = [];
            foreach ($entrypoints as $entry) {
                if (str_ends_with($entry, '.css')) {
                    $tags[] = '<link rel="stylesheet" href="/' . htmlspecialchars($entry, ENT_QUOTES) . '">';
                } else {
                    $tags[] = '<script type="module" src="/' . htmlspecialchars($entry, ENT_QUOTES) . '"></script>';
                }
            }
            return implode("\n", $tags);
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true) ?: [];
        $tags = [];
        $cssTags = [];
        $jsTags = [];

        foreach ($entrypoints as $entry) {
            $cleanEntry = ltrim($entry, '/');
            if (isset($manifest[$cleanEntry])) {
                $chunk = $manifest[$cleanEntry];
                $file = '/' . $this->buildDirectory . '/' . $chunk['file'];

                if (str_ends_with($file, '.css')) {
                    $cssTags[] = '<link rel="stylesheet" href="' . htmlspecialchars($file, ENT_QUOTES) . '">';
                } else {
                    $jsTags[] = '<script type="module" src="' . htmlspecialchars($file, ENT_QUOTES) . '"></script>';
                }

                // Attach any imported CSS chunks
                if (isset($chunk['css']) && is_array($chunk['css'])) {
                    foreach ($chunk['css'] as $cssFile) {
                        $cssTags[] = '<link rel="stylesheet" href="/' . htmlspecialchars($this->buildDirectory . '/' . $cssFile, ENT_QUOTES) . '">';
                    }
                }
            }
        }

        return implode("\n", array_unique(array_merge($cssTags, $jsTags)));
    }
}
