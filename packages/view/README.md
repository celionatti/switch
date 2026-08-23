# Switch View (`switch/view`)

> An expressive HTML tag-based template view engine with built-in UI components (`<x-card>`, `<x-button>`, `<x-alert>`, `<x-modal>`, `<x-skeleton>`, `<x-reactive>`), dual syntax parity (`@directive` & `<tag />`), security-by-default (XSS sanitizer, CSRF token, Honeypot bot defense, CSP Nonces), dot-notation property resolution, layout inheritance, and high-velocity template compilation.

---

## 📦 Installation

```bash
composer require switch/view
```

---

## 🚀 Quick Start

```php
use Switch\View\Engine\ViewEngine;
use Switch\View\View;

$engine = new ViewEngine(
    viewsPath: __DIR__ . '/views',
    cachePath: __DIR__ . '/storage/views'
);

View::setEngine($engine);

echo View::render('home', [
    'title' => 'Dashboard',
    'user' => ['name' => 'Alice', 'role' => 'Admin']
]);
```

---

## ⚡ Directives & HTML Tag Parity Reference

Every directive in Switch View supports both `@directive` and HTML `<tag>` syntax with 100% feature parity:

| Feature / Category | `@directive` Syntax | HTML Tag Directive Syntax | Output / Description |
|---|---|---|---|
| **CSRF Field** | `@csrf` | `<csrf />` or `<s-csrf />` | Hidden `_token` input field |
| **Method Spoofing** | `@method('PUT')` | `<method value="PUT" />` or `<s-method value="PUT" />` | Hidden `_method` input field |
| **Honeypot Protection** | `@honeypot` | `<honeypot />` or `<s-honeypot />` | Invisible honeypot trap fields |
| **CSP Nonce** | `<script @nonce>` | `<script <nonce />>` or `<s-nonce />` | Nonce attribute `nonce="..."` |
| **Live SPA Scripts** | `@liveScripts` | `<live-scripts />` or `<liveScripts />` | Switch Live reactive SPA bundle |
| **Notification Stream** | `@notificationStream` | `<notification-stream />` or `<notifications />` | Real-time SSE notification client bridge |
| **Head Meta / SEO** | `@head` | `<head-meta />` or `<head-tags />` | Dynamic SEO title, meta & OpenGraph |
| **Flash Messages** | `@flash('toast')` | `<flash mode="toast" position="bottom-right" />` | Toast / Alert flash renderer |
| **Old Input Value** | `@old('email', 'default')` | `<old name="email" default="default" />` | Repopulated form input value |
| **Form Errors** | `@error('email')...@enderror` | `<error field="email">...</error>` | Validation error container |
| **Session Messages** | `@session('status')...@endsession` | `<session key="status">...</session>` | Session flash message container |
| **Safe JSON Output** | `@json($data)` | `<json :data="$data" />` or `<s-json />` | XSS-safe JSON string |
| **Authentication** | `@auth...@endauth` | `<auth>...</auth>` or `<s-auth>` | Render for authenticated users |
| **Guest / Anonymous** | `@guest...@endguest` | `<guest>...</guest>` or `<s-guest>` | Render for unauthenticated guests |
| **Authorization (Can)** | `@can('edit', $post)...@endcan` | `<can ability="edit" :args="[$post]">...</can>` | Policy gate check (allowed) |
| **Authorization (Cannot)** | `@cannot('edit', $post)...@endcannot` | `<cannot ability="edit" :args="[$post]">...</cannot>` | Policy gate check (denied) |
| **Environment Check** | `@env('production')...@endenv` | `<env name="production">...</env>` | Conditional by active environment |
| **Production Check** | `@production...@endproduction` | `<production>...</production>` | Conditional for production mode |
| **Conditionals (If)** | `@if($c)...@elseif($c)...@else...@endif` | `<if cond="$c">...<elseif cond="$c" />...<else />...</if>` | PHP `if/elseif/else` branching |
| **Unless** | `@unless($c)...@endunless` | `<unless cond="$c">...</unless>` | Inverted condition (`if (!($c))`) |
| **Isset & Empty** | `@isset($v)...@endisset` / `@empty($v)...@endempty` | `<isset var="$v">...</isset>` / `<empty var="$v">...</empty>` | PHP `isset()` and `empty()` |
| **Foreach Loop** | `@foreach($items as $i)...@endforeach` | `<foreach items="$items" as="$i">...</foreach>` | Standard array iteration |
| **Forelse Loop** | `@forelse($items as $i)...@empty...@endforelse` | `<forelse items="$items" as="$i">...<empty />...</forelse>` | Iteration with fallback if empty |
| **For Loop** | `@for($i=0; $i<10; $i++)...@endfor` | `<for var="$i=0" cond="$i<10" incr="$i++">...</for>` | Standard counted loop |
| **While Loop** | `@while($cond)...@endwhile` | `<while cond="$cond">...</while>` | While loop condition |
| **Switch Statement** | `@switch($v) @case(1)...@break @default...@endswitch` | `<switch value="$v"><case value="1">...</case><break /><default>...</default></switch>` | Switch-case branching |
| **Layout Extension** | `@extends('layouts.app')` | `<layout name="layouts.app" />` or `<extends name="layouts.app" />` | Inherit parent layout |
| **Section Capture** | `@section('content')...@endsection` | `<section name="content">...</section>` | Define content section |
| **Section Yield** | `@yield('content', 'default')` | `<yield name="content" default="default" />` | Render section placeholder |
| **Include Partial** | `@include('partials.nav', $data)` | `<include file="partials.nav" :data="$data" />` or `<partial name="..." />` | Sub-template rendering |
| **Raw PHP** | `@php ... @endphp` | `<php>...</php>` | Embedded PHP execution |

---

## 🎨 HTML Tag Components

Switch View provides expressive Blade-style HTML Tag Components (`<x-component-name>`) out of the box:

### Built-in UI Components

#### `<x-card>`
```html
<x-card title="User Statistics">
    <p>Total Sales: $12,450</p>
    <x-slot name="footer">
        Updated 5 mins ago
    </x-slot>
</x-card>
```

#### `<x-button>`
```html
<x-button variant="primary" type="submit">Save Changes</x-button>
<x-button variant="danger">Delete</x-button>
```

#### `<x-alert>`
```html
<x-alert type="warning" dismissible>
    Your subscription expires in 3 days.
</x-alert>
```

#### `<x-input>`
```html
<x-input name="email" label="Email Address" type="email" value="user@example.com" error="Invalid email address" />
```

#### `<x-modal>`
```html
<x-modal id="deleteModal" title="Confirm Delete">
    Are you sure you want to delete this item?
    <x-slot name="footer">
        <x-button variant="secondary">Cancel</x-button>
        <x-button variant="danger">Delete</x-button>
    </x-slot>
</x-modal>
```

#### `<x-badge>`, `<x-avatar>`, `<x-spinner>`
```html
<x-badge color="success">Active</x-badge>
<x-avatar src="/img/user.jpg" alt="Alice" size="40px" />
<x-spinner size="24px" color="#3b82f6" />
```

#### Shimmer & Skeleton Loaders
```html
<!-- Card Skeleton -->
<x-skeleton type="card" />

<!-- Text Skeleton -->
<x-skeleton type="text" rows="4" />

<!-- Custom Shimmer Placeholder -->
<x-shimmer width="100%" height="40px" radius="0.5rem" />
```

---

## 🔒 Security by Default

### 1. Auto-Escaping Interpolation (`{{ $expr }}`)
All standard interpolations are XSS-escaped by default using `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.

Use `{!! $expr !!}` only when explicitly outputting trusted raw HTML.

### 2. CSRF Token Defense
```html
<!-- Tag Directive -->
<csrf />

<!-- @ Directive -->
@csrf

<!-- Generated Output -->
<input type="hidden" name="_token" value="4f8a9b...">
```

### 3. Honeypot Spam & Bot Protection
```html
<!-- Tag Directive -->
<honeypot />

<!-- @ Directive -->
@honeypot
```

### 4. Content Security Policy (CSP) Nonce
```html
<script <nonce />>
    console.log("Inline script with CSP Nonce!");
</script>
```

### 5. HTML XSS Sanitizer (`SecurityHelper::cleanHtml()`)
Strips dangerous tags (`<script>`, `<iframe>`, `<object>`), inline `on*` event attributes (`onload=`, `onclick=`), and `javascript:` URIs.

```php
use Switch\View\Security\SecurityHelper;

$safeHtml = SecurityHelper::cleanHtml($userSubmittedHtml);
```

---

## 🧩 Custom Component Registration

Register custom HTML tag components easily:

```php
use Switch\View\View;

View::component('custom-card', function (array $attr, string $slot, array $slots) {
    return '<div class="my-card"><h3>' . ($attr['title'] ?? '') . '</h3>' . $slot . '</div>';
});
```

Usage in template:
```html
<x-custom-card title="Welcome">
    This is my custom component body.
</x-custom-card>
```

---

## 📄 License
MIT License.
