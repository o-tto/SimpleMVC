<?php
/**
 * @var int    $status
 * @var string $title
 * @var string $message
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($status) ?> - <?= e($title) ?></title>
    <style>
        body { font-family: system-ui, Arial, sans-serif; background: #eee; margin: 0; }
        div { max-width: 40rem; margin: 3rem auto; padding: 1.5rem 2rem; background: #fff; border-radius: 4px; box-shadow: 0 0 6px rgba(0, 0, 0, .3); }
    </style>
</head>
<body>
<div>
    <h2><?= e($status) ?> - <?= e($title) ?></h2>
    <hr>
    <p>Something went wrong while processing your request.</p>
    <?php if ($message !== ''): ?>
        <pre><?= e($message) ?></pre>
    <?php endif; ?>
</div>
</body>
</html>
