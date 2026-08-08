<?php $this->extend('layouts.app'); ?>

<?php $this->startSection('title'); ?><?= htmlspecialchars((string) ($title), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?><?php $this->endSection(); ?>

<?php $this->startSection('content'); ?>
    <h1>Welcome to <?= htmlspecialchars((string) ($appName), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
    <p>Status: <span class="badge">Active</span></p>

    <?php if ($user): ?>
        <p>Logged in as: <strong><?= htmlspecialchars((string) ($this->get($user, 'name')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong> (<?= htmlspecialchars((string) ($this->get($user, 'role')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>)</p>
    <?php else: ?>
        <p>Guest User</p>
    <?php endif; ?>

    <h3>Database ORM Products</h3>
    <ul>
        <?php foreach ($products as $product): ?>
            <li><strong><?= htmlspecialchars((string) ($this->get($product, 'name')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong> — $<?= htmlspecialchars((string) ($this->get($product, 'price')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Framework Subsystems</h3>
    <ul>
        <?php foreach ($subsystems as $sys): ?>
            <li><?= htmlspecialchars((string) ($sys), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
<?php $this->endSection(); ?>
