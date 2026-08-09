# Switch HTTP Message (`switch/http-message`)

> PSR-7 HTTP Message & PSR-17 Factory implementation for Requests, Responses, ServerRequests, UploadedFiles, URIs, and Streams.

---

## 📦 Installation

```bash
composer require switch/http-message
```

---

## 🚀 Usage

```php
use Switch\Http\ServerRequest;
use Switch\Http\Response;

// Capture global HTTP server request
$request = ServerRequest::fromGlobals();

echo $request->getMethod();
echo $request->getUri()->getPath();

// Create HTTP response
$response = new Response(200, ['Content-Type' => 'application/json']);
```

---

## 📄 License
MIT License.
