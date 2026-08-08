<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $this->yieldSection('title', 'Switch Framework App'); ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; }
        .card { background: #1e293b; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .badge { background: #38bdf8; color: #0f172a; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <?= $this->yieldSection('content', ''); ?>
    </div>
</body>
</html>
