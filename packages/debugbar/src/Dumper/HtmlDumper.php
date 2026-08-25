<?php

declare(strict_types=1);

namespace Switch\DebugBar\Dumper;

/**
 * Ultra-fast, zero-dependency recursive HTML interactive tree dumper.
 * Produces modern, styled, expandable DOM elements with type badges, copy actions, and circular-reference safety.
 */
class HtmlDumper
{
    private static int $instanceCounter = 0;

    /**
     * Dump a variable into clean interactive HTML.
     */
    public static function dump(mixed $value, int $maxDepth = 5): string
    {
        $dumper = new self($maxDepth);
        return $dumper->render($value);
    }

    private array $visitedObjects = [];

    public function __construct(private readonly int $maxDepth = 5)
    {
    }

    public function render(mixed $value): string
    {
        self::$instanceCounter++;
        $id = 'sdb_dump_' . self::$instanceCounter;

        return '<div class="sdb-dumper" id="' . $id . '">' . $this->renderValue($value, 0) . '</div>';
    }

    private function renderValue(mixed $value, int $depth): string
    {
        if ($depth > $this->maxDepth) {
            return '<span class="sdb-val-truncated">[Max Depth Reached]</span>';
        }

        if (is_null($value)) {
            return '<span class="sdb-val-null">null</span>';
        }

        if (is_bool($value)) {
            return '<span class="sdb-val-bool">' . ($value ? 'true' : 'false') . '</span>';
        }

        if (is_int($value) || is_float($value)) {
            return '<span class="sdb-val-num">' . $value . '</span>';
        }

        if (is_string($value)) {
            return $this->renderString($value);
        }

        if (is_array($value)) {
            return $this->renderArray($value, $depth);
        }

        if (is_object($value)) {
            return $this->renderObject($value, $depth);
        }

        if (is_resource($value)) {
            return '<span class="sdb-val-resource">resource(' . get_resource_type($value) . ')</span>';
        }

        return '<span class="sdb-val-unknown">' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    private function renderString(string $value): string
    {
        $escaped = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $len = mb_strlen($value);

        if ($len > 160) {
            $short = htmlspecialchars(mb_substr($value, 0, 160), ENT_QUOTES, 'UTF-8');
            return '<span class="sdb-val-str sdb-str-expandable" title="String length: ' . $len . ' chars">'
                . '<span class="sdb-str-short">"' . $short . '…' . '"</span>'
                . '<span class="sdb-str-full" style="display:none;">"' . $escaped . '"</span>'
                . '<span class="sdb-badge-tag sdb-str-toggle" onclick="this.parentElement.querySelector(\'.sdb-str-short\').style.display = this.parentElement.querySelector(\'.sdb-str-short\').style.display===\'none\'?\'inline\':\'none\'; this.parentElement.querySelector(\'.sdb-str-full\').style.display = this.parentElement.querySelector(\'.sdb-str-full\').style.display===\'none\'?\'inline\':\'none\';">' . $len . ' chars</span>'
                . '</span>';
        }

        return '<span class="sdb-val-str" title="String length: ' . $len . ' chars">"' . $escaped . '"</span>';
    }

    private function renderArray(array $array, int $depth): string
    {
        $count = count($array);
        if ($count === 0) {
            return '<span class="sdb-val-arr">array:0 []</span>';
        }

        $open = $depth < 2 ? ' open' : '';
        $html = '<details class="sdb-tree-node"' . $open . '>';
        $html .= '<summary class="sdb-tree-summary"><span class="sdb-val-type">array:' . $count . '</span> <span class="sdb-bracket">[</span></summary>';
        $html .= '<div class="sdb-tree-children">';

        $rendered = 0;
        foreach ($array as $key => $val) {
            if ($rendered++ >= 60) {
                $html .= '<div class="sdb-tree-item"><span class="sdb-val-truncated">… and ' . ($count - 60) . ' more items</span></div>';
                break;
            }

            $keyEscaped = htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8');
            $html .= '<div class="sdb-tree-item">';
            $html .= '<span class="sdb-key">' . (is_string($key) ? '"' . $keyEscaped . '"' : $keyEscaped) . '</span> <span class="sdb-assign">=&gt;</span> ';
            $html .= $this->renderValue($val, $depth + 1);
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '<div class="sdb-bracket-close">]</div>';
        $html .= '</details>';

        return $html;
    }

    private function renderObject(object $object, int $depth): string
    {
        $hash = spl_object_hash($object);
        $className = get_class($object);

        if (isset($this->visitedObjects[$hash])) {
            return '<span class="sdb-val-recursion">#circular ' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '</span>';
        }

        $this->visitedObjects[$hash] = true;

        if ($object instanceof \DateTimeInterface) {
            return '<span class="sdb-val-date">' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . ' ("' . $object->format('Y-m-d H:i:s.u') . '")</span>';
        }

        if ($object instanceof \Closure) {
            $ref = new \ReflectionFunction($object);
            $file = $ref->getFileName() ? basename($ref->getFileName()) . ':' . $ref->getStartLine() : 'native';
            return '<span class="sdb-val-closure">Closure (' . $file . ')</span>';
        }

        $ref = new \ReflectionClass($object);
        $props = $ref->getProperties();
        $handledProps = [];

        $open = $depth < 1 ? ' open' : '';
        $html = '<details class="sdb-tree-node"' . $open . '>';
        $html .= '<summary class="sdb-tree-summary"><span class="sdb-val-class">' . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . '</span> <span class="sdb-bracket">{</span></summary>';
        $html .= '<div class="sdb-tree-children">';

        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $name = $prop->getName();
            $handledProps[$name] = true;
            $modifier = $prop->isPublic() ? '+' : ($prop->isProtected() ? '#' : '-');
            
            $val = $prop->isInitialized($object) ? $prop->getValue($object) : '[uninitialized]';

            $html .= '<div class="sdb-tree-item">';
            $html .= '<span class="sdb-modifier" title="' . ($prop->isPublic() ? 'public' : ($prop->isProtected() ? 'protected' : 'private')) . '">' . $modifier . '</span>';
            $html .= '<span class="sdb-prop-name">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</span>: ';
            $html .= $prop->isInitialized($object) ? $this->renderValue($val, $depth + 1) : '<span class="sdb-val-uninit">uninitialized</span>';
            $html .= '</div>';
        }

        // Handle dynamic properties (e.g. stdClass)
        $dynamicVars = get_object_vars($object);
        foreach ($dynamicVars as $name => $val) {
            if (isset($handledProps[$name])) {
                continue;
            }

            $html .= '<div class="sdb-tree-item">';
            $html .= '<span class="sdb-modifier" title="public">+</span>';
            $html .= '<span class="sdb-prop-name">' . htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') . '</span>: ';
            $html .= $this->renderValue($val, $depth + 1);
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '<div class="sdb-bracket-close">}</div>';
        $html .= '</details>';

        return $html;
    }
}
