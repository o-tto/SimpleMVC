<?php

declare(strict_types=1);

namespace mvcsystem;

use RuntimeException;

/**
 * Renders plain PHP templates from the view directory.
 *
 * A view named "Index" for the "Home" controller resolves to either
 *   view/Home/Index.php           (a complete page), or
 *   view/Home/Index.subview.php   (wrapped in view/Home/_layout.php as $content).
 * File names are matched case-insensitively, so behaviour is the same on
 * Linux, macOS and Windows.
 */
final class View
{
    public static function directory(): string
    {
        return dirname(__DIR__) . '/view';
    }

    /**
     * @param array<string, mixed> $vars
     */
    public static function render(string $controller, string $view, array $vars = []): string
    {
        $dir = self::findEntry(self::directory(), $controller);
        if ($dir === null || !is_dir($dir)) {
            throw new RuntimeException("View directory for '{$controller}' does not exist.");
        }

        $page = self::findEntry($dir, $view . '.php');
        if ($page !== null) {
            return self::renderFile($page, $vars);
        }

        $subview = self::findEntry($dir, $view . '.subview.php');
        if ($subview === null) {
            throw new RuntimeException("View '{$controller}/{$view}' does not exist.");
        }

        $content = self::renderFile($subview, $vars);
        $layout = self::findEntry($dir, '_layout.php');
        if ($layout === null) {
            return $content;
        }

        return self::renderFile($layout, ['content' => $content] + $vars);
    }

    /**
     * Render a single template file in an isolated scope.
     *
     * @param array<string, mixed> $vars
     */
    public static function renderFile(string $__file, array $__vars = []): string
    {
        ob_start();
        try {
            (static function () use ($__file, $__vars): void {
                extract($__vars, EXTR_SKIP);
                include $__file;
            })();

            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    /** Find a directory entry by name, ignoring case. */
    private static function findEntry(string $dir, string $name): ?string
    {
        if (!is_dir($dir)) {
            return null;
        }
        if (file_exists($dir . '/' . $name)) {
            return $dir . '/' . $name;
        }
        foreach (scandir($dir) ?: [] as $entry) {
            if (strcasecmp($entry, $name) === 0) {
                return $dir . '/' . $entry;
            }
        }

        return null;
    }
}
