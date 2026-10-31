<?php
/**
 * @var string $content Rendered subview.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SimpleMVC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(to left, #485563, #29323c);
            min-height: 100vh;
            color: #fff;
        }
        a { color: #9ecbff; }
        .nav-link { color: #fff; }
        .nav-link:hover, .nav-link:focus { color: #9ecbff; }
        code { color: #ffc6d9; }
        footer { border-top: 1px solid rgba(255, 255, 255, .4); }
    </style>
</head>
<body>
<div class="container py-4">
    <header class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">SimpleMVC</h3>
            <small>Version 2.0.0</small>
        </div>
        <nav class="nav">
            <a class="nav-link" href="/Home/Index">Home</a>
            <a class="nav-link" href="/Home/Features">Features</a>
            <a class="nav-link" href="/Home/Docs">Docs</a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer class="mt-5 pt-3">
        <p class="small">SimpleMVC &mdash; a simple PHP MVC framework. Licensed under the Apache License, 2.0.</p>
    </footer>
</div>
</body>
</html>
