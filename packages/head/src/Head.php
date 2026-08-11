<?php

declare(strict_types=1);

namespace Switch\Head;

/**
 * Static Facade for HeadManager.
 *
 * @method static HeadManager setTitle(string $title)
 * @method static HeadManager setTitleTemplate(string $template)
 * @method static string getTitle()
 * @method static HeadManager setCharset(string $charset)
 * @method static HeadManager setCanonical(string $url)
 * @method static HeadManager meta(string $name, string $content, string $type = 'name')
 * @method static HeadManager description(string $description)
 * @method static HeadManager keywords(string|array $keywords)
 * @method static HeadManager robots(string $robots)
 * @method static HeadManager og(string $property, string $content)
 * @method static HeadManager twitter(string $name, string $content)
 * @method static HeadManager addFavicon(string $href, string $rel = 'icon', string $type = 'image/x-icon')
 * @method static HeadManager addAppleTouchIcon(string $href, string $sizes = '180x180')
 * @method static HeadManager addLink(string $rel, string $href, array $attributes = [])
 * @method static HeadManager addPreload(string $href, string $as, ?string $type = null)
 * @method static HeadManager addPreconnect(string $href, bool $crossorigin = false)
 * @method static HeadManager addScript(string $src, array $attributes = [])
 * @method static HeadManager addInlineScript(string $code, array $attributes = [])
 * @method static HeadManager addStyle(string $href, array $attributes = [])
 * @method static HeadManager addJsonLd(array $data)
 * @method static string render()
 */
class Head
{
    private static ?HeadManager $instance = null;

    public static function getInstance(): HeadManager
    {
        if (self::$instance === null) {
            self::$instance = new HeadManager();
        }
        return self::$instance;
    }

    public static function setInstance(HeadManager $manager): void
    {
        self::$instance = $manager;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        $instance = self::getInstance();
        return call_user_func_array([$instance, $method], $arguments);
    }
}
