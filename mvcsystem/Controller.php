<?php

declare(strict_types=1);

namespace mvcsystem;

/**
 * Base class for controllers.
 *
 * Every public, non-static method declared in a concrete controller is an
 * action reachable at /{Controller}/{action}. Methods on this base class are
 * never routable. Actions return a Response (or a string, sent as HTML).
 */
abstract class Controller
{
    /**
     * @param string $name   Controller name without the "Controller" suffix, e.g. "Home".
     * @param string $action The action being executed, e.g. "index".
     */
    public function __construct(
        protected readonly string $name,
        protected readonly string $action,
    ) {
    }

    /**
     * Render a view. Defaults to the view named after the current action.
     *
     * @param array<string, mixed> $vars
     */
    protected function view(array $vars = [], ?string $view = null, int $status = 200): Response
    {
        return new Response(View::render($this->name, $view ?? $this->action, $vars), $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    protected function text(string $body, int $status = 200): Response
    {
        return Response::text($body, $status);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return new Response(
            json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            $status,
            ['Content-Type' => 'application/json'],
        );
    }

    protected function error(int $status = 500, string $message = ''): Response
    {
        return ErrorPage::response($status, $message);
    }
}
