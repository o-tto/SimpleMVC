<?php

declare(strict_types=1);

namespace mvcsystem;

use Throwable;

/**
 * Builds error responses from view/_error/{status}.php, falling back to plain text.
 */
final class ErrorPage
{
    private const TITLES = [
        400 => 'Bad Request',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
    ];

    public static function response(int $status, string $message = ''): Response
    {
        $title = self::TITLES[$status] ?? 'Error';
        $file = View::directory() . '/_error/' . $status . '.php';
        if (!is_file($file)) {
            $file = View::directory() . '/_error/500.php';
        }

        try {
            if (is_file($file)) {
                $body = View::renderFile($file, [
                    'status' => $status,
                    'title' => $title,
                    'message' => $message,
                ]);

                return new Response($body, $status);
            }
        } catch (Throwable) {
            // Fall through to the plain-text response below.
        }

        return Response::text("{$status} {$title}", $status);
    }
}
