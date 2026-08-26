<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Switch\Controller\Controller;
use Switch\Foundation\Auth\Facade\Auth;
use Switch\Foundation\Bridge\Facade\Bridge;
use Switch\Foundation\Collection\Collection;
use Switch\Foundation\Context\Facade\Context;
use Switch\Foundation\Data\Facade\Data;
use Switch\Foundation\Mailer\Mailable;
use Switch\Foundation\Notification\Facade\Notification;
use Switch\Foundation\Notification\Notification as BaseNotification;

class ShowcaseController extends Controller
{
    /**
     * Display the comprehensive Switch Framework Feature Showcase.
     */
    public function index(): string
    {
        // 1. Context API Demo Computation
        Context::provide('app.theme', 'dark');
        Context::provide('app.tenant', [
            'id' => 'tenant_9981',
            'name' => 'Acme Cloud Solutions',
            'tier' => 'enterprise',
            'features' => ['live_spa', 'audit_flow', 'crypto_tokens', 'sse_stream'],
        ]);
        Context::provide('client.user', [
            'name' => 'Sarah Connor',
            'role' => 'Lead Architect',
            'preferred_locale' => 'en_US',
        ]);

        $currentTheme = Context::use('app.theme');
        $tenantName = Context::use('app.tenant.name');
        $clientContextJson = json_encode(Context::getClientPayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // 2. Data & Mocking Subsystem Computation
        $mockUsers = Data::mock('user', 3, [
            'role' => 'Staff Engineer',
        ]);
        $mockProducts = Data::mock('product', 3);
        $sampleFakes = [
            'uuid' => Data::fake('uuid'),
            'email' => Data::fake('email'),
            'name' => Data::fake('name'),
            'price' => Data::fake('price', 10, 500),
            'date' => Data::fake('date'),
        ];

        // 3. Fluent Collection Engine Demo Computation
        $rawEmployees = [
            ['id' => 1, 'name' => 'Alice Johnson', 'department' => 'Engineering', 'salary' => 125000, 'is_active' => true, 'parent_id' => null],
            ['id' => 2, 'name' => 'Bob Smith', 'department' => 'Engineering', 'salary' => 95000, 'is_active' => true, 'parent_id' => 1],
            ['id' => 3, 'name' => 'Charlie Rose', 'department' => 'Design', 'salary' => 90000, 'is_active' => false, 'parent_id' => null],
            ['id' => 4, 'name' => 'Diana Prince', 'department' => 'Product', 'salary' => 140000, 'is_active' => true, 'parent_id' => null],
            ['id' => 5, 'name' => 'Evan Wright', 'department' => 'Engineering', 'salary' => 88000, 'is_active' => true, 'parent_id' => 1],
            ['id' => 6, 'name' => 'Fiona Gallagher', 'department' => 'Design', 'salary' => 110000, 'is_active' => true, 'parent_id' => 3],
        ];

        $employeeCollection = collect($rawEmployees);
        $activeEmployees = $employeeCollection->where('is_active', true);
        $avgActiveSalary = $activeEmployees->avg('salary');
        $departments = $employeeCollection->groupBy('department')->map(fn($group) => count($group))->toArray();
        $salarySummary = [
            'total' => $activeEmployees->sum('salary'),
            'average' => $avgActiveSalary,
            'max' => $activeEmployees->max('salary'),
            'count' => $activeEmployees->count(),
        ];
        $orgTree = $employeeCollection->toTree('id', 'parent_id')->toArray();

        // 4. Bridge & Webhook HMAC Signing Simulation
        $webhookPayload = [
            'event' => 'order.completed',
            'order_id' => 'ord_' . bin2hex(random_bytes(4)),
            'customer' => 'alex@example.com',
            'total' => 349.99,
            'timestamp' => time(),
        ];
        $webhookSecret = 'whsec_switch_prod_key_77a9f4c3';
        $payloadJson = json_encode($webhookPayload);
        $webhookSignature = hash_hmac('sha256', $payloadJson, $webhookSecret);

        // 5. Mailer Subsystem Preview
        $sampleMailable = (new Mailable())
            ->to('client@example.com', 'Client Name')
            ->subject('Your Switch Framework Report')
            ->html('<h1>Hello from Switch</h1><p>This is a compiled HTML mailable demonstration.</p>')
            ->text("Hello from Switch\n\nThis is a plain-text version compiled automatically.");

        // 6. Passwordless Auth Token Simulation
        $sampleMagicToken = passwordless()->generateToken('engineer@switch-framework.dev', 'login', [], 15);
        $sampleVerifyUrl = passwordless()->buildVerifyUrl($sampleMagicToken->token);

        return $this->view('showcase.index', [
            'title' => 'Features Showcase — Switch Framework',
            // Context
            'currentTheme' => $currentTheme,
            'tenantName' => $tenantName,
            'clientContextJson' => $clientContextJson,
            // Data / Mock
            'mockUsers' => $mockUsers,
            'mockProducts' => $mockProducts,
            'sampleFakes' => $sampleFakes,
            // Collection
            'employeeCount' => $employeeCollection->count(),
            'activeCount' => $activeEmployees->count(),
            'departments' => $departments,
            'salarySummary' => $salarySummary,
            'orgTree' => $orgTree,
            // Bridge
            'webhookPayloadJson' => $payloadJson,
            'webhookSecret' => $webhookSecret,
            'webhookSignature' => $webhookSignature,
            // Mailer
            'mailableSubject' => $sampleMailable->getSubject(),
            'mailableHtml' => $sampleMailable->getHtmlBody(),
            'mailableText' => $sampleMailable->getTextBody(),
            // Passwordless
            'sampleMagicToken' => $sampleMagicToken,
            'sampleVerifyUrl' => $sampleVerifyUrl,
        ]);
    }

    /**
     * AJAX endpoint to generate dynamic Mock data on demand.
     */
    public function generateMocks(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $type = $body['type'] ?? 'user';
        $count = min(10, max(1, (int) ($body['count'] ?? 3)));

        $data = Data::mock($type, $count);
        $this->toast("Generated {$count} mock {$type} records!", 'success');

        return $this->json([
            'success' => true,
            'type' => $type,
            'count' => $count,
            'records' => $data,
        ]);
    }

    /**
     * AJAX endpoint to simulate outbound webhook dispatching.
     */
    public function dispatchWebhook(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $event = $body['event'] ?? 'user.registered';
        $email = $body['email'] ?? 'test@example.com';

        $payload = [
            'event' => $event,
            'data' => [
                'email' => $email,
                'occurred_at' => date('c'),
            ],
        ];

        $secret = 'whsec_demo_secret_key_8841';
        $json = json_encode($payload);
        $sig = hash_hmac('sha256', $json, $secret);

        $this->toast("Outbound webhook '{$event}' signed & prepared with HMAC-SHA256!", 'success');

        return $this->json([
            'success' => true,
            'event' => $event,
            'signature' => $sig,
            'payload' => $payload,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Switch-Signature' => $sig,
                'X-Switch-Timestamp' => (string) time(),
                'X-Switch-Idempotency' => 'idem_' . bin2hex(random_bytes(8)),
            ],
        ]);
    }
}
