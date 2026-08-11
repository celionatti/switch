<?php

declare(strict_types=1);

namespace Switch\Head;

class HeadManager
{
    private string $charset = 'UTF-8';
    private string $title = '';
    private string $titleTemplate = '%s';
    private array $metaTags = [];
    private array $linkTags = [];
    private array $scripts = [];
    private array $styles = [];
    private array $jsonLd = [];
    private ?string $canonical = null;

    public function __construct()
    {
        // Default sensible viewports and defaults
        $this->meta('viewport', 'width=device-width, initial-scale=1.0');
    }

    /**
     * Set page title.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set page title format template (e.g. "%s - Switch Framework").
     */
    public function setTitleTemplate(string $template): self
    {
        $this->titleTemplate = $template;
        return $this;
    }

    public function getTitle(): string
    {
        if ($this->title === '') {
            return '';
        }
        return sprintf($this->titleTemplate, $this->title);
    }

    /**
     * Set page charset.
     */
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Set canonical URL.
     */
    public function setCanonical(string $url): self
    {
        $this->canonical = $url;
        return $this;
    }

    /**
     * Add standard meta tag by name/property and content.
     */
    public function meta(string $name, string $content, string $type = 'name'): self
    {
        $this->metaTags[] = [
            $type => $name,
            'content' => $content,
        ];
        return $this;
    }

    /**
     * Set meta description.
     */
    public function description(string $description): self
    {
        return $this->meta('description', $description);
    }

    /**
     * Set meta keywords.
     */
    public function keywords(string|array $keywords): self
    {
        $val = is_array($keywords) ? implode(', ', $keywords) : $keywords;
        return $this->meta('keywords', $val);
    }

    /**
     * Set meta robots directive.
     */
    public function robots(string $robots): self
    {
        return $this->meta('robots', $robots);
    }

    /**
     * Add OpenGraph meta tag.
     */
    public function og(string $property, string $content): self
    {
        $prop = str_starts_with($property, 'og:') ? $property : 'og:' . $property;
        return $this->meta($prop, $content, 'property');
    }

    /**
     * Add Twitter card meta tag.
     */
    public function twitter(string $name, string $content): self
    {
        $key = str_starts_with($name, 'twitter:') ? $name : 'twitter:' . $name;
        return $this->meta($key, $content, 'name');
    }

    /**
     * Add Favicon icon.
     */
    public function addFavicon(string $href, string $rel = 'icon', string $type = 'image/x-icon'): self
    {
        return $this->addLink($rel, $href, ['type' => $type]);
    }

    /**
     * Add Apple Touch Icon.
     */
    public function addAppleTouchIcon(string $href, string $sizes = '180x180'): self
    {
        return $this->addLink('apple-touch-icon', $href, ['sizes' => $sizes]);
    }

    /**
     * Add Link tag (stylesheet, preload, preconnect, canonical, etc.).
     */
    public function addLink(string $rel, string $href, array $attributes = []): self
    {
        $this->linkTags[] = array_merge([
            'rel' => $rel,
            'href' => $href,
        ], $attributes);
        return $this;
    }

    /**
     * Add Preload resource link.
     */
    public function addPreload(string $href, string $as, ?string $type = null): self
    {
        $attrs = ['as' => $as];
        if ($type !== null) {
            $attrs['type'] = $type;
        }
        return $this->addLink('preload', $href, $attrs);
    }

    /**
     * Add Preconnect domain link.
     */
    public function addPreconnect(string $href, bool $crossorigin = false): self
    {
        $attrs = [];
        if ($crossorigin) {
            $attrs['crossorigin'] = 'anonymous';
        }
        return $this->addLink('preconnect', $href, $attrs);
    }

    /**
     * Add Script tag.
     */
    public function addScript(string $src, array $attributes = []): self
    {
        $this->scripts[] = array_merge(['src' => $src], $attributes);
        return $this;
    }

    /**
     * Add Inline Script tag.
     */
    public function addInlineScript(string $code, array $attributes = []): self
    {
        $this->scripts[] = ['_inline' => $code, 'attributes' => $attributes];
        return $this;
    }

    /**
     * Add Stylesheet tag.
     */
    public function addStyle(string $href, array $attributes = []): self
    {
        return $this->addLink('stylesheet', $href, $attributes);
    }

    /**
     * Add Schema.org JSON-LD structured data.
     */
    public function addJsonLd(array $data): self
    {
        $this->jsonLd[] = $data;
        return $this;
    }

    /**
     * Render all head HTML tags into a formatted string.
     */
    public function render(): string
    {
        $html = [];

        // 1. Charset
        $html[] = sprintf('<meta charset="%s">', htmlspecialchars($this->charset, ENT_QUOTES, 'UTF-8'));

        // 2. Title
        $fullTitle = $this->getTitle();
        if ($fullTitle !== '') {
            $html[] = sprintf('<title>%s</title>', htmlspecialchars($fullTitle, ENT_QUOTES, 'UTF-8'));
        }

        // 3. Canonical URL
        if ($this->canonical !== null) {
            $html[] = sprintf('<link rel="canonical" href="%s">', htmlspecialchars($this->canonical, ENT_QUOTES, 'UTF-8'));
        }

        // 4. Meta Tags
        foreach ($this->metaTags as $meta) {
            $attrs = [];
            foreach ($meta as $k => $v) {
                $attrs[] = sprintf('%s="%s"', $k, htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'));
            }
            $html[] = sprintf('<meta %s>', implode(' ', $attrs));
        }

        // 5. Link Tags
        foreach ($this->linkTags as $link) {
            $attrs = [];
            foreach ($link as $k => $v) {
                if (is_bool($v)) {
                    if ($v) {
                        $attrs[] = $k;
                    }
                } else {
                    $attrs[] = sprintf('%s="%s"', $k, htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'));
                }
            }
            $html[] = sprintf('<link %s>', implode(' ', $attrs));
        }

        // 6. Scripts
        foreach ($this->scripts as $script) {
            if (isset($script['_inline'])) {
                $attrsStr = '';
                if (!empty($script['attributes'])) {
                    $a = [];
                    foreach ($script['attributes'] as $k => $v) {
                        $a[] = is_bool($v) ? ($v ? $k : '') : sprintf('%s="%s"', $k, htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'));
                    }
                    $attrsStr = ' ' . implode(' ', array_filter($a));
                }
                $html[] = sprintf('<script%s>%s</script>', $attrsStr, $script['_inline']);
            } else {
                $attrs = [];
                foreach ($script as $k => $v) {
                    if (is_bool($v)) {
                        if ($v) {
                            $attrs[] = $k;
                        }
                    } else {
                        $attrs[] = sprintf('%s="%s"', $k, htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'));
                    }
                }
                $html[] = sprintf('<script %s></script>', implode(' ', $attrs));
            }
        }

        // 7. Schema.org JSON-LD Structured Data
        foreach ($this->jsonLd as $json) {
            $jsonString = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $html[] = sprintf('<script type="application/ld+json">%s</script>', $jsonString);
        }

        return implode("\n    ", $html);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
