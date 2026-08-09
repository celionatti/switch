# Switch Events (`switch/events`)

> Lightweight PSR-14 Event Dispatcher, Stoppable Events, and Listener Provider implementation.

---

## 📦 Installation

```bash
composer require switch/events
```

---

## 🚀 Usage

```php
use Switch\Event\EventDispatcher;
use Switch\Event\ListenerProvider;

$provider = new ListenerProvider();
$provider->addListener(UserRegisteredEvent::class, function (UserRegisteredEvent $event) {
    // Send welcome email
});

$dispatcher = new EventDispatcher($provider);
$dispatcher->dispatch(new UserRegisteredEvent($user));
```

---

## 📄 License
MIT License.
