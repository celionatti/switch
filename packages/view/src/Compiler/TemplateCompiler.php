<?php

declare(strict_types=1);

namespace Switch\View\Compiler;

use Switch\View\Component\ComponentRegistry;
use Switch\View\Security\SecurityHelper;

class TemplateCompiler
{
    /**
     * Compile template source string into executable PHP code.
     */
    public function compile(string $contents): string
    {
        // 1. Comments {{-- comment --}}
        $contents = preg_replace('/\{\{--(.*?)--\}\}/s', '<?php /*$1*/ ?>', $contents) ?? $contents;

        // 2. Raw PHP Blocks <php>...</php> and @php...@endphp
        $contents = preg_replace('/<php>(.*?)<\/php>/s', '<?php $1 ?>', $contents) ?? $contents;
        $contents = preg_replace('/@php\b(.*?)@endphp/s', '<?php $1 ?>', $contents) ?? $contents;

        // 3. Security, Framework Tags & @ Directives (CSRF, Method, Honeypot, Nonce, Live, Notifications, Head, Flash, Old, Errors, Session, JSON)
        $contents = $this->compileFrameworkTagsAndDirectives($contents);

        // 4. Raw Interpolation {!! $expr !!}
        $contents = preg_replace_callback('/\{\!\!\s*(.*?)\s*\!\!\}/s', function ($m) {
            $expr = $this->compileDotSyntax($m[1]);
            return "<?= (string) ({$expr}); ?>";
        }, $contents) ?? $contents;

        // 5. Escaped Interpolation {{ $expr }}
        $contents = preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/s', function ($m) {
            $expr = $this->compileDotSyntax($m[1]);
            return "<?= htmlspecialchars((string) ({$expr}), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>";
        }, $contents) ?? $contents;

        // 6. Components: <x-name ...>...</x-name> and self-closing <x-name ... />
        $contents = $this->compileComponentTags($contents);

        // 7. Layouts, Extends, Sections, Yield, Partials & Includes
        $contents = $this->compileLayoutsAndPartials($contents);

        // 8. Control Structures (Auth, Guest, Can, Env, If, Unless, Isset, Empty, Loops, Switch)
        $contents = $this->compileControlStructures($contents);

        return $contents;
    }

    /**
     * Compile security helpers, framework tools, and their HTML tag equivalents.
     */
    private function compileFrameworkTagsAndDirectives(string $contents): string
    {
        // CSRF: @csrf  <==>  <csrf /> or <csrf></csrf> or <s-csrf />
        $contents = preg_replace('/<(?:s-)?csrf(?:\s*\/|\s*>\s*<\/(?:s-)?csrf)?>/i', '<?= \Switch\View\Security\SecurityHelper::csrfField(); ?>', $contents) ?? $contents;
        $contents = preg_replace('/@csrf\b/i', '<?= \Switch\View\Security\SecurityHelper::csrfField(); ?>', $contents) ?? $contents;

        // HTTP Method Spoofing: @method('PUT')  <==>  <method value="PUT" /> or <s-method value="PUT" />
        $contents = preg_replace_callback('/<(?:s-)?method\s+(?:value|type|name|method)=[\'"]([^\'"]+)[\'"]\s*\/?>/i', function ($m) {
            return '<?= \Switch\View\Security\SecurityHelper::methodField(\'' . addslashes($m[1]) . '\'); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@method\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', function ($m) {
            return '<?= \Switch\View\Security\SecurityHelper::methodField(\'' . addslashes($m[1]) . '\'); ?>';
        }, $contents) ?? $contents;

        // Honeypot: @honeypot('name', 'time')  <==>  <honeypot name="..." time="..." /> or <s-honeypot />
        $contents = preg_replace_callback('/<(?:s-)?honeypot(?:\s+name=[\'"]([^\'"]+)[\'"])?(?:\s+time=[\'"]([^\'"]+)[\'"])?\s*\/?>/i', function ($m) {
            $name = !empty($m[1]) ? '\'' . addslashes($m[1]) . '\'' : '\'my_name_hp\'';
            $time = !empty($m[2]) ? '\'' . addslashes($m[2]) . '\'' : '\'my_time_hp\'';
            return '<?= \Switch\View\Security\SecurityHelper::honeypot(' . $name . ', ' . $time . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@honeypot(?:\s*\(\s*(?:[\'"]([^\'"]+)[\'"])?(?:,\s*[\'"]([^\'"]+)[\'"])?\s*\))?/i', function ($m) {
            $name = !empty($m[1]) ? '\'' . addslashes($m[1]) . '\'' : '\'my_name_hp\'';
            $time = !empty($m[2]) ? '\'' . addslashes($m[2]) . '\'' : '\'my_time_hp\'';
            return '<?= \Switch\View\Security\SecurityHelper::honeypot(' . $name . ', ' . $time . '); ?>';
        }, $contents) ?? $contents;

        // CSP Nonce: @nonce  <==>  <nonce /> or <s-nonce />
        $contents = preg_replace('/<(?:s-)?nonce\s*\/?>/i', 'nonce="<?= \Switch\View\Security\SecurityHelper::getCspNonce(); ?>"', $contents) ?? $contents;
        $contents = preg_replace('/@nonce\b/i', 'nonce="<?= \Switch\View\Security\SecurityHelper::getCspNonce(); ?>"', $contents) ?? $contents;

        // Live Scripts (SPA & reactivity): @liveScripts  <==>  <live-scripts /> or <liveScripts /> or <s-live-scripts />
        $contents = preg_replace('/<(?:s-)?(?:live-scripts|liveScripts|livescripts)\s*\/?>/i', '<?= function_exists(\'live_scripts\') ? live_scripts() : \'\'; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@(?:liveScripts|live_scripts|livescripts)\b/i', '<?= function_exists(\'live_scripts\') ? live_scripts() : \'\'; ?>', $contents) ?? $contents;

        // Notification Stream: @notificationStream  <==>  <notification-stream /> or <notifications /> or <s-notification-stream />
        $contents = preg_replace('/<(?:s-)?(?:notification-stream|notificationStream|notifications)\s*\/?>/i', '<?= function_exists(\'notification_stream\') ? notification_stream() : \'\'; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@(?:notificationStream|notification_stream|notifications)\b/i', '<?= function_exists(\'notification_stream\') ? notification_stream() : \'\'; ?>', $contents) ?? $contents;

        // Head Meta / SEO: @head  <==>  <head-meta /> or <head-tags /> or <s-head />
        $contents = preg_replace('/<(?:s-head|head-meta|head-tags|head:tags)\s*\/?>/i', '<?= function_exists(\'head\') ? head()->render() : \'\'; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@head\b/i', '<?= function_exists(\'head\') ? head()->render() : \'\'; ?>', $contents) ?? $contents;

        // Flash Messages: @flash('toast')  <==>  <flash mode="toast" position="bottom-right" /> or <s-flash />
        $contents = preg_replace_callback('/<(?:s-)?flash(?:\s+mode=[\'"]([^\'"]+)[\'"])?(?:\s+position=[\'"]([^\'"]+)[\'"])?\s*\/?>/i', function ($m) {
            if (empty($m[1]) && empty($m[2])) {
                return '<?= function_exists(\'flash_render\') ? flash_render() : \'\'; ?>';
            }
            $mode = !empty($m[1]) ? '\'' . $m[1] . '\'' : '\'toast\'';
            $pos = !empty($m[2]) ? '\'' . $m[2] . '\'' : '\'bottom-right\'';
            return '<?= function_exists(\'flash_render\') ? flash_render(' . $mode . ', [\'position\' => ' . $pos . ']) : \'\'; ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@flash(?:\s*\(\s*(?:[\'"]([^\'"]+)[\'"])?(?:\s*,\s*(.*?))?\s*\))?/i', function ($m) {
            if (empty($m[1])) {
                return '<?= function_exists(\'flash_render\') ? flash_render() : \'\'; ?>';
            }
            $mode = '\'' . $m[1] . '\'';
            $opts = !empty($m[2]) ? ', ' . $this->compileDotSyntax($m[2]) : '';
            return '<?= function_exists(\'flash_render\') ? flash_render(' . $mode . $opts . ') : \'\'; ?>';
        }, $contents) ?? $contents;

        // Form Old Value: @old('email', 'default')  <==>  <old name="email" default="default" /> or <s-old name="email" />
        $contents = preg_replace_callback('/<(?:s-)?old\s+(?:name|field)=[\'"]([^\'"]+)[\'"](?:\s+default=[\'"]([^\'"]*)[\'"])?\s*\/?>/i', function ($m) {
            $default = isset($m[2]) ? '\'' . addslashes($m[2]) . '\'' : '\'\'';
            return '<?= function_exists(\'old\') ? old(\'' . addslashes($m[1]) . '\', ' . $default . ') : ' . $default . '; ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@old\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*(.*?))?\s*\)/i', function ($m) {
            $default = !empty($m[2]) ? $this->compileDotSyntax($m[2]) : '\'\'';
            return '<?= function_exists(\'old\') ? old(\'' . addslashes($m[1]) . '\', ' . $default . ') : ' . $default . '; ?>';
        }, $contents) ?? $contents;

        // JSON Output: @json($data)  <==>  <json data="$data" /> or <json :data="$data" /> or <s-json />
        $contents = preg_replace_callback('/<(?:s-)?json\s+(?::data|data)=[\'"]([^\'"]+)[\'"]\s*\/?>/i', function ($m) {
            $expr = $this->compileDotSyntax($m[1]);
            return '<?= \\Switch\\View\\Security\\SecurityHelper::safeJson(' . $expr . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@json\s*\((.*?)\)/s', function ($m) {
            $expr = $this->compileDotSyntax($m[1]);
            return '<?= \\Switch\\View\\Security\\SecurityHelper::safeJson(' . $expr . '); ?>';
        }, $contents) ?? $contents;
        // Context API: Provide / Consume
        $contents = $this->compileContextDirectives($contents);

        // Data & Mock: Static datasets and mock data generation
        $contents = $this->compileDataDirectives($contents);

        // View Fragment Caching: @cache / <cache>
        $contents = $this->compileCacheDirectives($contents);

        return $contents;
    }

    /**
     * Compile View Fragment Caching directives (@cache / <cache>).
     */
    private function compileCacheDirectives(string $contents): string
    {
        // Tag Open: <cache key="..." ttl="..." tags="..."> or <cache :key="..." :ttl="...">
        $contents = preg_replace_callback('/<(?:s-)?cache\b([^>]*)>/i', function ($m) {
            $attrs = $m[1];

            // Extract key
            $key = '\'default\'';
            if (preg_match('/(?:^|\s):key=[\'"]([^\'"]+)[\'"]/i', $attrs, $km)) {
                $key = $this->compileDotSyntax($km[1]);
            } elseif (preg_match('/(?:^|\s)key=[\'"]([^\'"]+)[\'"]/i', $attrs, $km)) {
                $key = '\'' . addslashes($km[1]) . '\'';
            }

            // Extract ttl
            $ttl = 'null';
            if (preg_match('/(?:^|\s):ttl=[\'"]([^\'"]+)[\'"]/i', $attrs, $tm)) {
                $ttl = $this->compileDotSyntax($tm[1]);
            } elseif (preg_match('/(?:^|\s)ttl=[\'"]([^\'"]+)[\'"]/i', $attrs, $tm)) {
                $ttl = is_numeric($tm[1]) ? $tm[1] : '\'' . addslashes($tm[1]) . '\'';
            }

            // Extract tags
            $tags = '[]';
            if (preg_match('/(?:^|\s):tags=[\'"]([^\'"]+)[\'"]/i', $attrs, $tgm)) {
                $tags = $this->compileDotSyntax($tgm[1]);
            } elseif (preg_match('/(?:^|\s)tags=[\'"]([^\'"]+)[\'"]/i', $attrs, $tgm)) {
                $tags = '\'' . addslashes($tgm[1]) . '\'';
            }

            return '<?php if (! \\Switch\\View\\Cache\\FragmentCache::start(' . $key . ', ' . $ttl . ', ' . $tags . ')): ?>';
        }, $contents) ?? $contents;

        // Tag Close: </cache> or </s-cache>
        $contents = preg_replace('/<\/(?:s-)?cache\s*>/i', '<?php echo \\Switch\\View\\Cache\\FragmentCache::end(); endif; ?>', $contents) ?? $contents;

        // Directive Open: @cache($key, $ttl, $tags)
        $contents = preg_replace_callback('/@cache\s*\((.*?)\)/s', function ($m) {
            $args = $this->compileDotSyntax($m[1]);
            return '<?php if (! \\Switch\\View\\Cache\\FragmentCache::start(' . $args . ')): ?>';
        }, $contents) ?? $contents;

        // Directive Close: @endcache
        $contents = preg_replace('/@endcache\b/i', '<?php echo \\Switch\\View\\Cache\\FragmentCache::end(); endif; ?>', $contents) ?? $contents;

        return $contents;
    }

    /**
     * Compile Context API directives (provide/consume).
     */
    private function compileContextDirectives(string $contents): string
    {
        // Context Provider (block): <context name="theme" :value="['mode' => 'dark']">...</context>
        $contents = preg_replace_callback('/<(?:s-)?(?:context|provide)\s+name=[\'"]([^\'"]+)[\'"]\s+(?::value|value)=((["\'])(.*?)\3)\s*>/i', function ($m) {
            $expr = $this->compileDotSyntax($m[4]);
            return '<?php \\Switch\\Foundation\\Context\\Facade\\Context::context(\'' . addslashes($m[1]) . '\')->push(' . $expr . '); ?>';
        }, $contents) ?? $contents;

        // Context Provider end: </context> or </provide> or </s-context>
        $contents = preg_replace('/<\/(?:s-)?(?:context|provide)>/i', '<?php \\Switch\\Foundation\\Context\\Facade\\Context::getManager()->context(array_key_last(\\Switch\\Foundation\\Context\\Facade\\Context::all()) ?? \'_\')->pop(); ?>', $contents) ?? $contents;

        // @context('theme', ['mode' => 'dark'])...@endcontext
        $contents = preg_replace_callback('/@context\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*(.*?)\s*\)/s', function ($m) {
            $expr = $this->compileDotSyntax($m[2]);
            return '<?php \\Switch\\Foundation\\Context\\Facade\\Context::context(\'' . addslashes($m[1]) . '\')->push(' . $expr . '); ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/@endcontext\b/i', '<?php \\Switch\\Foundation\\Context\\Facade\\Context::getManager()->context(array_key_last(\\Switch\\Foundation\\Context\\Facade\\Context::all()) ?? \'_\')->pop(); ?>', $contents) ?? $contents;

        // Context Consumer: <use-context name="theme" as="$theme" /> or <consume name="..." as="..." />
        $contents = preg_replace_callback('/<(?:s-)?(?:use-context|consume)\s+name=[\'"]([^\'"]+)[\'"]\s+as=[\'"](\$[^\'"]+)[\'"]\s*\/?>/i', function ($m) {
            $varName = ltrim($m[2], '$');
            return '<?php $' . $varName . ' = \\Switch\\Foundation\\Context\\Facade\\Context::use(\'' . addslashes($m[1]) . '\'); ?>';
        }, $contents) ?? $contents;

        // @useContext('theme', '$theme') / @consume('theme', '$theme')
        $contents = preg_replace_callback('/@(?:useContext|consume)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"](\$[^\'"]+)[\'"]\s*\)/i', function ($m) {
            $varName = ltrim($m[2], '$');
            return '<?php $' . $varName . ' = \\Switch\\Foundation\\Context\\Facade\\Context::use(\'' . addslashes($m[1]) . '\'); ?>';
        }, $contents) ?? $contents;

        return $contents;
    }

    /**
     * Compile Data & Mock directives.
     */
    private function compileDataDirectives(string $contents): string
    {
        // Static Data: <data source="countries" as="$countries" />
        $contents = preg_replace_callback('/<(?:s-)?data\s+source=[\'"]([^\'"]+)[\'"]\s+as=[\'"](\$[^\'"]+)[\'"]\s*\/?>/i', function ($m) {
            $varName = ltrim($m[2], '$');
            return '<?php $' . $varName . ' = function_exists(\'data\') ? data(\'' . addslashes($m[1]) . '\') : []; ?>';
        }, $contents) ?? $contents;

        // Mock Data: <data mock="user" count="5" as="$users" /> or <mock blueprint="user" count="5" as="$users" />
        $contents = preg_replace_callback('/<(?:s-)?(?:data\s+mock|mock(?:\s+blueprint)?)=[\'"]([^\'"]+)[\'"]\s+count=[\'"](\d+)[\'"]\s+as=[\'"](\$[^\'"]+)[\'"]\s*\/?>/i', function ($m) {
            $varName = ltrim($m[3], '$');
            return '<?php $' . $varName . ' = function_exists(\'mock\') ? mock(\'' . addslashes($m[1]) . '\', ' . (int) $m[2] . ') : []; ?>';
        }, $contents) ?? $contents;

        // @data('countries', '$countries')
        $contents = preg_replace_callback('/@data\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"](\$[^\'"]+)[\'"]\s*\)/i', function ($m) {
            $varName = ltrim($m[2], '$');
            return '<?php $' . $varName . ' = function_exists(\'data\') ? data(\'' . addslashes($m[1]) . '\') : []; ?>';
        }, $contents) ?? $contents;

        // @mock('user', 5, '$users')
        $contents = preg_replace_callback('/@mock\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*(\d+)\s*,\s*[\'"](\$[^\'"]+)[\'"]\s*\)/i', function ($m) {
            $varName = ltrim($m[3], '$');
            return '<?php $' . $varName . ' = function_exists(\'mock\') ? mock(\'' . addslashes($m[1]) . '\', ' . (int) $m[2] . ') : []; ?>';
        }, $contents) ?? $contents;

        return $contents;
    }

    /**
     * Compile layout inheritance, section captures, yields, and view includes/partials.
     */
    private function compileLayoutsAndPartials(string $contents): string
    {
        // Layout Extension: @extends('layouts.app') / @layout('layouts.app')  <==>  <layout name="layouts.app" /> or <extends name="layouts.app" />
        $contents = preg_replace_callback('/<(?:s-)?(?:layout|extends)\s+[^>]*?(?:name|layout|file)=[\'"]([^\'"]+)[\'"][^>]*?\/?>/i', function ($m) {
            return '<?php $this->extend(\'' . $m[1] . '\'); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?(?:layout|extends)>/i', '', $contents) ?? $contents;
        $contents = preg_replace_callback('/@(?:extends|layout)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', function ($m) {
            return '<?php $this->extend(\'' . $m[1] . '\'); ?>';
        }, $contents) ?? $contents;

        // Section Start: @section('content')  <==>  <section name="content"> or <s-section name="content">
        $contents = preg_replace_callback('/<(?:s-)?section\s+[^>]*?name=[\'"]([^\'"]+)[\'"][^>]*?>/i', function ($m) {
            return '<?php $this->startSection(\'' . $m[1] . '\'); ?>';
        }, $contents) ?? $contents;

        // Inline Section: @section('title', 'My Title')
        $contents = preg_replace_callback('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]*)[\'"]\s*\)/i', function ($m) {
            return '<?php $this->startSection(\'' . $m[1] . '\'); echo \'' . addslashes($m[2]) . '\'; $this->endSection(); ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace_callback('/@section\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', function ($m) {
            return '<?php $this->startSection(\'' . $m[1] . '\'); ?>';
        }, $contents) ?? $contents;

        // Section End: </section> or </s-section> or @endsection or @stop
        $contents = preg_replace('/<\/(?:s-)?section>/i', '<?php $this->endSection(); ?>', $contents) ?? $contents;
        $contents = preg_replace('/@(endsection|stop)\b/i', '<?php $this->endSection(); ?>', $contents) ?? $contents;

        // Yield Section: @yield('content', 'Default')  <==>  <yield name="content" default="Default" /> or <s-yield ... />
        $contents = preg_replace_callback('/<(?:s-)?yield\s+name=[\'"]([^\'"]+)[\'"](?:\s+default=[\'"]([^\'"]*)[\'"])?\s*\/?>/i', function ($m) {
            $name = $m[1];
            $default = isset($m[2]) ? '\'' . addslashes($m[2]) . '\'' : '\'\'';
            return '<?= $this->yieldSection(\'' . $name . '\', ' . $default . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@yield\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*(?:[\'"]([^\'"]*)[\'"]|(.*?)))?\s*\)/i', function ($m) {
            $name = $m[1];
            $default = isset($m[2]) && $m[2] !== ''
                ? '\'' . addslashes($m[2]) . '\''
                : (!empty($m[3]) ? $this->compileDotSyntax($m[3]) : '\'\'');
            return '<?= $this->yieldSection(\'' . $name . '\', ' . $default . '); ?>';
        }, $contents) ?? $contents;

        // Partials / Includes: @include('partials.header', $data)  <==>  <include file="partials.header" /> or <partial name="partials.header" />
        $contents = preg_replace_callback('/<(?:s-)?(?:include|partial)\s+(?:file|name)=[\'"]([^\'"]+)[\'"](?:\s+(?:data|with)=[\'"]([^\'"]*)[\'"])?\s*\/?>/i', function ($m) {
            $file = $m[1];
            $data = !empty($m[2]) ? 'array_merge($__data, ' . $this->compileDotSyntax($m[2]) . ')' : '$__data';
            return '<?= $this->render(\'' . $file . '\', ' . $data . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@(?:include|partial)\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*(.*?))?\s*\)/i', function ($m) {
            $file = $m[1];
            $data = !empty($m[2]) ? 'array_merge($__data, ' . $this->compileDotSyntax($m[2]) . ')' : '$__data';
            return '<?= $this->render(\'' . $file . '\', ' . $data . '); ?>';
        }, $contents) ?? $contents;

        return $contents;
    }

    /**
     * Compile control structures (Auth, Gate, Form Error, Session, If, Unless, Loops).
     */
    private function compileControlStructures(string $contents): string
    {
        // 1. Auth & Guest: @auth / @guest  <==>  <auth>...</auth> / <guest>...</guest>
        $contents = preg_replace('/<(?:s-)?auth>/i', '<?php if (function_exists(\'auth\') && auth()->check()): ?>', $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?auth>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@auth\b/i', '<?php if (function_exists(\'auth\') && auth()->check()): ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endauth\b/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace('/<(?:s-)?guest>/i', '<?php if (!function_exists(\'auth\') || !auth()->check()): ?>', $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?guest>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@guest\b/i', '<?php if (!function_exists(\'auth\') || !auth()->check()): ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endguest\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 2. Gate Authorization: @can('edit', $post) / @cannot  <==>  <can ability="edit" :args="[$post]">...</can>
        $contents = preg_replace_callback('/<(?:s-)?can\s+(?:ability|do)=[\'"]([^\'"]+)[\'"](?:\s+:args=[\'"]([^\'"]+)[\'"])?\s*>/i', function ($m) {
            $ability = '\'' . addslashes($m[1]) . '\'';
            $args = !empty($m[2]) ? '...' . $this->compileDotSyntax($m[2]) : '';
            return '<?php if (function_exists(\'gate\') && gate()->allows(' . $ability . ($args !== '' ? ', ' . $args : '') . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?can>/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/@can\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*(.*?))?\s*\)/i', function ($m) {
            $ability = '\'' . addslashes($m[1]) . '\'';
            $args = !empty($m[2]) ? ', ' . $this->compileDotSyntax($m[2]) : '';
            return '<?php if (function_exists(\'gate\') && gate()->allows(' . $ability . $args . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/@endcan\b/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/<(?:s-)?cannot\s+(?:ability|do)=[\'"]([^\'"]+)[\'"](?:\s+:args=[\'"]([^\'"]+)[\'"])?\s*>/i', function ($m) {
            $ability = '\'' . addslashes($m[1]) . '\'';
            $args = !empty($m[2]) ? '...' . $this->compileDotSyntax($m[2]) : '';
            return '<?php if (!function_exists(\'gate\') || !gate()->allows(' . $ability . ($args !== '' ? ', ' . $args : '') . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?cannot>/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/@cannot\s*\(\s*[\'"]([^\'"]+)[\'"](?:\s*,\s*(.*?))?\s*\)/i', function ($m) {
            $ability = '\'' . addslashes($m[1]) . '\'';
            $args = !empty($m[2]) ? ', ' . $this->compileDotSyntax($m[2]) : '';
            return '<?php if (!function_exists(\'gate\') || !gate()->allows(' . $ability . $args . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/@endcannot\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 3. Validation Error: @error('email')  <==>  <error field="email">...</error>
        $contents = preg_replace_callback('/<(?:s-)?error\s+(?:field|name)=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $field = '\'' . addslashes($m[1]) . '\'';
            return '<?php if (function_exists(\'errors\') && errors()->has(' . $field . ')): $message = errors()->first(' . $field . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?error>/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/@error\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', function ($m) {
            $field = '\'' . addslashes($m[1]) . '\'';
            return '<?php if (function_exists(\'errors\') && errors()->has(' . $field . ')): $message = errors()->first(' . $field . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/@enderror\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 4. Session Value Block: @session('status')  <==>  <session key="status">...</session>
        $contents = preg_replace_callback('/<(?:s-)?session\s+(?:key|name)=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $key = '\'' . addslashes($m[1]) . '\'';
            return '<?php if (function_exists(\'session\') && session()->has(' . $key . ')): $value = session()->get(' . $key . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?session>/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/@session\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', function ($m) {
            $key = '\'' . addslashes($m[1]) . '\'';
            return '<?php if (function_exists(\'session\') && session()->has(' . $key . ')): $value = session()->get(' . $key . '); ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/@endsession\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 5. Environment & Production: @env('prod') / @production  <==>  <env name="prod">...</env> / <production>...</production>
        $contents = preg_replace_callback('/<(?:s-)?env\s+name=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            return '<?php if (function_exists(\'app\') && app()->isEnv(\'' . addslashes($m[1]) . '\')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?env>/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/@env\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/i', function ($m) {
            return '<?php if (function_exists(\'app\') && app()->isEnv(\'' . addslashes($m[1]) . '\')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/@endenv\b/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace('/<(?:s-)?production>/i', '<?php if (function_exists(\'app\') && app()->isProduction()): ?>', $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?production>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@production\b/i', '<?php if (function_exists(\'app\') && app()->isProduction()): ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endproduction\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 6. Conditionals: If / Elseif / Else / Endif
        $contents = preg_replace_callback('/<(?:s-)?if\s+cond=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php if (' . $cond . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@if\s*\((.*?)\)/s', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php if (' . $cond . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace_callback('/<(?:s-)?elseif\s+cond=[\'"]([^\'"]+)[\'"]\s*\/?>/i', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php elseif (' . $cond . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@elseif\s*\((.*?)\)/s', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php elseif (' . $cond . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<(?:s-)?else\s*\/?>/i', '<?php else: ?>', $contents) ?? $contents;
        $contents = preg_replace('/@else\b/i', '<?php else: ?>', $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?if>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endif\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 7. Unless / Endunless
        $contents = preg_replace_callback('/<(?:s-)?unless\s+cond=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php if (!(' . $cond . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@unless\s*\((.*?)\)/s', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php if (!(' . $cond . ')): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?unless>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endunless\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 8. Isset & Empty
        $contents = preg_replace_callback('/<(?:s-)?isset\s+(?:var|name)=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $var = $this->compileDotSyntax($m[1]);
            return '<?php if (isset(' . $var . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@isset\s*\((.*?)\)/s', function ($m) {
            $var = $this->compileDotSyntax($m[1]);
            return '<?php if (isset(' . $var . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?isset>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endisset\b/i', '<?php endif; ?>', $contents) ?? $contents;

        $contents = preg_replace_callback('/<(?:s-)?empty\s+(?:var|name)=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $var = $this->compileDotSyntax($m[1]);
            return '<?php if (empty(' . $var . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@empty\s*\((.*?)\)/s', function ($m) {
            $var = $this->compileDotSyntax($m[1]);
            return '<?php if (empty(' . $var . ')): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace('/<\/(?:s-)?empty>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endempty\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 9. Forelse / Empty / Endforelse
        $contents = preg_replace_callback('/<(?:s-)?forelse\s+items=[\'"]([^\'"]+)[\'"]\s+as=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $items = $this->compileDotSyntax($m[1]);
            return '<?php if (!empty(' . $items . ')): foreach (' . $items . ' as ' . $m[2] . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@forelse\s*\(\s*(.*?)\s+as\s+(.*?)\s*\)/s', function ($m) {
            $items = $this->compileDotSyntax($m[1]);
            return '<?php if (!empty(' . $items . ')): foreach (' . $items . ' as ' . $m[2] . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<(?:s-)?empty\s*\/?>/i', '<?php endforeach; else: ?>', $contents) ?? $contents;
        $contents = preg_replace('/@empty\b/i', '<?php endforeach; else: ?>', $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?forelse>/i', '<?php endif; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endforelse\b/i', '<?php endif; ?>', $contents) ?? $contents;

        // 10. Foreach / Endforeach
        $contents = preg_replace_callback('/<(?:s-)?foreach\s+items=[\'"]([^\'"]+)[\'"]\s+as=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $items = $this->compileDotSyntax($m[1]);
            return '<?php foreach (' . $items . ' as ' . $m[2] . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@foreach\s*\(\s*(.*?)\s+as\s+(.*?)\s*\)/s', function ($m) {
            $items = $this->compileDotSyntax($m[1]);
            return '<?php foreach (' . $items . ' as ' . $m[2] . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?foreach>/i', '<?php endforeach; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endforeach\b/i', '<?php endforeach; ?>', $contents) ?? $contents;

        // 11. For / Endfor
        $contents = preg_replace_callback('/<(?:s-)?for\s+var=[\'"]([^\'"]+)[\'"]\s+cond=[\'"]([^\'"]+)[\'"]\s+incr=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            return '<?php for (' . $m[1] . '; ' . $m[2] . '; ' . $m[3] . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@for\s*\(\s*(.*?)\s*;\s*(.*?)\s*;\s*(.*?)\s*\)/s', function ($m) {
            return '<?php for (' . $m[1] . '; ' . $m[2] . '; ' . $m[3] . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?for>/i', '<?php endfor; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endfor\b/i', '<?php endfor; ?>', $contents) ?? $contents;

        // 12. While / Endwhile
        $contents = preg_replace_callback('/<(?:s-)?while\s+cond=[\'"]([^\'"]+)[\'"]\s*>/i', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php while (' . $cond . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@while\s*\((.*?)\)/s', function ($m) {
            $cond = $this->compileDotSyntax($m[1]);
            return '<?php while (' . $cond . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?while>/i', '<?php endwhile; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endwhile\b/i', '<?php endwhile; ?>', $contents) ?? $contents;

        // 13. Switch / Case / Break / Default / Endswitch
        $contents = preg_replace_callback('/<(?:s-)?switch\s+(?:value|var)=((["\'])(.*?)\2)\s*>/i', function ($m) {
            $expr = $this->compileDotSyntax($m[3]);
            return '<?php switch (' . $expr . '): ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@switch\s*\((.*?)\)/s', function ($m) {
            $expr = $this->compileDotSyntax($m[1]);
            return '<?php switch (' . $expr . '): ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace_callback('/<(?:s-)?case\s+value=((["\'])(.*?)\2)\s*>/i', function ($m) {
            $val = $this->compileDotSyntax($m[3]);
            return '<?php case ' . $val . ': ?>';
        }, $contents) ?? $contents;
        $contents = preg_replace_callback('/@case\s*\((.*?)\)/s', function ($m) {
            $val = $this->compileDotSyntax($m[1]);
            return '<?php case ' . $val . ': ?>';
        }, $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?case>/i', '', $contents) ?? $contents;

        $contents = preg_replace('/<(?:s-)?break\s*\/?>/i', '<?php break; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@break\b/i', '<?php break; ?>', $contents) ?? $contents;

        $contents = preg_replace('/<(?:s-)?default\s*>/i', '<?php default: ?>', $contents) ?? $contents;
        $contents = preg_replace('/@default\b/i', '<?php default: ?>', $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?default>/i', '', $contents) ?? $contents;

        $contents = preg_replace('/<\/(?:s-)?switch>/i', '<?php endswitch; ?>', $contents) ?? $contents;
        $contents = preg_replace('/@endswitch\b/i', '<?php endswitch; ?>', $contents) ?? $contents;

        return $contents;
    }

    /**
     * Compile <x-component-name ...> and <x-component-name ... /> tags.
     */
    private function compileComponentTags(string $contents): string
    {
        // Attribute matcher that respects quoted strings containing > (e.g. :state="['a' => 'b']")
        $attrPattern = '((?:[^"\'>]|"[^"]*"|\'[^\']*\')*)';

        // 1. Pair component tags: <x-card title="...">slot</x-card>
        $patternPair = '/<x-([a-zA-Z0-9_\-\.]+)\s*' . $attrPattern . '\s*>(.*?)<\/x-\1>/s';

        // 2. Self-closing component tags: <x-card title="..." />
        $patternSelfClosing = '/<x-([a-zA-Z0-9_\-\.]+)\s*' . $attrPattern . '\s*\/?>/s';

        // Compile pair tags first (to capture nested slots)
        $contents = preg_replace_callback($patternPair, function ($matches) {
            $name = strtolower($matches[1]);
            $rawAttr = $matches[2];
            $inner = $matches[3];

            $attributesPhp = $this->parseAttributesPhp($rawAttr);
            [$slotPhp, $slotsPhp] = $this->extractSlots($inner);

            return "<?= \\Switch\\View\\Component\\ComponentRegistry::render('{$name}', {$attributesPhp}, {$slotPhp}, {$slotsPhp}); ?>";
        }, $contents) ?? $contents;

        // Compile remaining self-closing tags
        $contents = preg_replace_callback($patternSelfClosing, function ($matches) {
            $name = strtolower($matches[1]);
            $rawAttr = $matches[2];

            if (str_starts_with(trim($rawAttr), '/')) {
                return $matches[0];
            }

            $attributesPhp = $this->parseAttributesPhp($rawAttr);

            return "<?= \\Switch\\View\\Component\\ComponentRegistry::render('{$name}', {$attributesPhp}, '', []); ?>";
        }, $contents) ?? $contents;

        return $contents;
    }

    /**
     * Parse HTML attribute string into a PHP array code string.
     * E.g.: `title="Hello" :user="$user" dismissible` -> `['title' => 'Hello', 'user' => $user, 'dismissible' => true]`
     */
    private function parseAttributesPhp(string $attrString): string
    {
        if (trim($attrString) === '') {
            return '[]';
        }

        $items = [];
        $pattern = '/(?::([a-zA-Z0-9_\-]+)|([a-zA-Z0-9_\-]+))(?:=(?:"([^"]*)"|\'([^\']*)\'))?/';

        preg_match_all($pattern, $attrString, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $isDynamic = !empty($m[1]);
            $key = addslashes($isDynamic ? $m[1] : $m[2]);

            if ($key === '/') {
                continue;
            }

            $value = $m[3] ?? ($m[4] ?? null);

            if ($value !== null) {
                if ($isDynamic) {
                    $expr = $this->compileDotSyntax($value);
                    $items[] = "'{$key}' => ({$expr})";
                } else {
                    $escapedVal = addslashes($value);
                    $items[] = "'{$key}' => '{$escapedVal}'";
                }
            } else {
                $items[] = "'{$key}' => true";
            }
        }

        return '[' . implode(', ', $items) . ']';
    }

    /**
     * Extract default slot and named slots (<x-slot name="title">...</x-slot>).
     */
    private function extractSlots(string $innerContent): array
    {
        $slots = [];

        // Match <x-slot name="header">...</x-slot> or <x-slot:header>...</x-slot:header>
        $pattern = '/<x-slot(?::([a-zA-Z0-9_\-]+)|\s+name=[\'"]([^\'"]+)[\'"])\s*>(.*?)<\/x-slot(?::\1)?\s*>/s';

        $slotBody = preg_replace_callback($pattern, function ($m) use (&$slots) {
            $name = !empty($m[1]) ? $m[1] : $m[2];
            $content = trim($m[3]);
            $slots[$name] = $content;
            return '';
        }, $innerContent) ?? $innerContent;

        $slotBody = trim($slotBody);

        // Prepare slot PHP
        $slotPhp = $slotBody !== '' ? var_export($slotBody, true) : "''";

        $slotsPhpItems = [];
        foreach ($slots as $name => $content) {
            $slotsPhpItems[] = "'" . addslashes($name) . "' => " . var_export($content, true);
        }
        $slotsPhp = '[' . implode(', ', $slotsPhpItems) . ']';

        return [$slotPhp, $slotsPhp];
    }

    /**
     * Transform dot syntax access: $user.name -> $this->get($user, 'name')
     */
    private function compileDotSyntax(string $expression): string
    {
        return preg_replace_callback('/(\$[a-zA-Z_][a-zA-Z0-9_]*)(?:\.([a-zA-Z_][a-zA-Z0-9_]*))+/', function ($matches) {
            $parts = explode('.', ltrim($matches[0], '$'));
            $root = '$' . array_shift($parts);
            foreach ($parts as $part) {
                $root = "\$this->get({$root}, '{$part}')";
            }
            return $root;
        }, $expression) ?? $expression;
    }
}
