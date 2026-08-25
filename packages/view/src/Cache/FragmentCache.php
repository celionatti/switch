<?php

declare(strict_types=1);

namespace Switch\View\Cache;

use Switch\Foundation\Cache\Facade\Cache;

class FragmentCache
{
    /**
     * @var array<int, array{key: string, ttl: int|null, tags: array<string>}>
     */
    private static array $stack = [];

    /**
     * In-memory fallback cache when CacheManager is not initialized.
     *
     * @var array<string, array{content: string, expires: int|null, tags: array<string>}>
     */
    private static array $memoryCache = [];

    /**
     * Start a cached fragment block.
     *
     * @param string|array|object $key Cache key or model/array key components
     * @param int|null $ttl Time to live in seconds (null = forever)
     * @param string|array $tags Cache tags for targeted invalidation
     * @return bool True if cache hit (content was served), False if cache miss (rendering needed)
     */
    public static function start(string|array|object $key, ?int $ttl = null, string|array $tags = []): bool
    {
        $normalizedKey = self::normalizeKey($key);
        $normalizedTags = is_string($tags) ? array_filter(array_map('trim', explode(',', $tags))) : (array) $tags;

        // Check cache hit
        $cached = self::get($normalizedKey);
        if ($cached !== null) {
            echo $cached;
            return true;
        }

        // Cache miss: start output buffering and push to stack
        self::$stack[] = [
            'key' => $normalizedKey,
            'ttl' => $ttl,
            'tags' => $normalizedTags,
        ];

        ob_start();
        return false;
    }

    /**
     * End a cached fragment block, store the buffered content, and return it.
     */
    public static function end(): string
    {
        if (empty(self::$stack)) {
            return '';
        }

        $config = array_pop(self::$stack);
        $content = ob_get_clean() ?: '';

        self::put($config['key'], $content, $config['ttl'], $config['tags']);

        return $content;
    }

    /**
     * Flush all fragment caches matching given tags.
     */
    public static function flush(string|array $tags = []): void
    {
        $tagList = is_string($tags) ? array_filter(array_map('trim', explode(',', $tags))) : (array) $tags;

        if (class_exists(Cache::class)) {
            try {
                if (!empty($tagList)) {
                    Cache::tags($tagList)->flush();
                } else {
                    Cache::flush();
                }
            } catch (\Throwable) {
                // Fallback
            }
        }

        if (empty($tagList)) {
            self::$memoryCache = [];
        } else {
            foreach (self::$memoryCache as $k => $item) {
                if (!empty(array_intersect($item['tags'], $tagList))) {
                    unset(self::$memoryCache[$k]);
                }
            }
        }
    }

    /**
     * Retrieve item from cache.
     */
    private static function get(string $key): ?string
    {
        if (class_exists(Cache::class)) {
            try {
                $val = Cache::get($key);
                if ($val !== null) {
                    return (string) $val;
                }
            } catch (\Throwable) {
                // Fallback to memory
            }
        }

        if (isset(self::$memoryCache[$key])) {
            $item = self::$memoryCache[$key];
            if ($item['expires'] === null || $item['expires'] >= time()) {
                return $item['content'];
            }
            unset(self::$memoryCache[$key]);
        }

        return null;
    }

    /**
     * Store item in cache.
     */
    private static function put(string $key, string $content, ?int $ttl = null, array $tags = []): void
    {
        if (class_exists(Cache::class)) {
            try {
                $store = !empty($tags) ? Cache::tags($tags) : null;
                if ($store) {
                    $store->put($key, $content, $ttl ?? 3600);
                } else {
                    Cache::put($key, $content, $ttl ?? 3600);
                }
            } catch (\Throwable) {
                // Fallback to memory
            }
        }

        self::$memoryCache[$key] = [
            'content' => $content,
            'expires' => $ttl !== null ? time() + $ttl : null,
            'tags' => $tags,
        ];
    }

    /**
     * Normalize key into unique deterministic string.
     */
    public static function normalizeKey(string|array|object $key): string
    {
        if (is_string($key)) {
            return 'view_frag_' . md5($key);
        }

        if (is_array($key)) {
            $parts = array_map(function ($part) {
                if (is_object($part)) {
                    return self::modelKey($part);
                }
                return (string) $part;
            }, $key);
            return 'view_frag_' . md5(implode(':', $parts));
        }

        if (is_object($key)) {
            return 'view_frag_' . md5(self::modelKey($key));
        }

        return 'view_frag_' . md5(serialize($key));
    }

    private static function modelKey(object $model): string
    {
        $id = $model->id ?? spl_object_hash($model);
        $updated = $model->updated_at ?? '';
        if ($updated instanceof \DateTimeInterface) {
            $updated = $updated->format(\DateTimeInterface::ATOM);
        }
        return get_class($model) . ':' . $id . ':' . (string) $updated;
    }
}
