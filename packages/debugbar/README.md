# ⚡ Switch DebugBar

A blazing fast, zero-overhead, responsive developer debug bar designed specifically for the **Switch Framework**.

---

## 🌟 Why Switch DebugBar is More Advanced Than Others

- 🚀 **Zero Bloat & Zero External Dependencies**: 100% self-contained vanilla JavaScript and scoped CSS. No jQuery, no FontAwesome, no 2MB bundles.
- ⚡ **Zero Overhead When Inactive**: Produces 0ms performance penalty in production or when `APP_DEBUG=false`.
- 📱 **100% Responsive Design**: Sleek glassmorphic Obsidian theme with adaptive mobile bottom sheet drawers, desktop dock, and minimizable pulsing pill badge.
- 🔄 **Real-Time AJAX & SPA History**: Automatically intercepts fetch/XHR and Switch Live partial requests, allowing instant switching between previous requests without page reloads.
- 🗄️ **N+1 and Slow Query Detection**: Groups duplicate SQL queries, calculates execution time percentages, and flags slow queries (>50ms).
- 🌲 **Interactive Tree Variable Dumper**: Recursive expandable DOM tree with syntax highlighting, type badges, copy values, and circular-reference protection.
- ⏱️ **Micro-Timelines & Checkpoints**: Measure custom blocks of code effortlessly with `debugbar_measure()` or `DebugBar::startMeasure()`.
- 🔒 **Security & Privacy**: Automatically redacts sensitive passwords, secrets, and auth tokens from headers, environment variables, and config dumps.

---

## 📦 Installation

```bash
composer require switch/debugbar --dev
```

---

## 🚀 Quick Start

### 1. Zero-Config Auto Injection
When `APP_DEBUG=true` is configured in your `.env`, Switch Framework automatically attaches the `DebugBarMiddleware` and injects the DebugBar into all HTML responses.

### 2. Manual Injection via Helpers

```php
// In your HTML or layout template:
<?= debugbar_render() ?>
```

---

## 🛠️ Usage Examples

### Inspect Variables
```php
// Using helper
debug($user, ['cart' => $cartItems]);

// Using Facade
use Switch\DebugBar\Facade\DebugBar;

DebugBar::info('User profile loaded successfully.');
DebugBar::warning('API rate limit reaching threshold.');
DebugBar::error('Failed to sync cache.');
```

### Performance Benchmarking
```php
// Benchmark a closure
$result = debugbar_measure('External API Call', function () {
    return Http::get('https://api.example.com/data');
});

// Or use start/stop measures
debugbar_start_measure('heavy_calculation', 'Heavy Math Calculation');
$data = performMath();
debugbar_stop_measure('heavy_calculation');
```

### Add Custom Data Collectors
```php
use Switch\DebugBar\Collectors\AbstractCollector;

class CustomMetricsCollector extends AbstractCollector
{
    public function getName(): string { return 'metrics'; }
    public function getTitle(): string { return 'Custom Metrics'; }
    public function getIcon(): string { return '📊'; }
    public function getBadge(): ?string { return 'Active'; }
    
    public function collect(): array
    {
        return [
            'uptime' => '99.99%',
            'active_workers' => 4,
        ];
    }
}

debugbar()->addCollector(new CustomMetricsCollector());
```

---

## ⌨️ Keyboard Shortcuts

- `Alt + D` or `Ctrl + Shift + D`: Toggle DebugBar (Minimize / Restore)
- `Esc`: Close open drawer inspector panel

---

## 📄 License

The Switch Framework DebugBar is open-source software licensed under the [MIT license](LICENSE).
