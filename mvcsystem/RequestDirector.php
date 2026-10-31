<?php

declare(strict_types=1);

namespace mvcsystem;

use ReflectionClass;
use ReflectionMethod;
use Throwable;

/**
 * Front controller: maps /{Controller}/{action} to controller\{Controller}Controller::{action}().
 *
 *   /             -> redirect to /Home/Index
 *   /Docs         -> redirect to /Home/Docs   (if HomeController has a docs action)
 *   /Order        -> redirect to /Order/Index (if OrderController exists)
 *   /Home/Docs    -> HomeController::docs()
 */
final class RequestDirector
{
    private const SEGMENT_PATTERN = '/^[A-Za-z][A-Za-z0-9_]*$/';

    public function __construct(
        private readonly string $controllerNamespace = 'controller\\',
        private readonly string $defaultController = 'Home',
        private readonly bool $debug = false,
    ) {
    }

    /** Handle the current request from the PHP superglobals and send the response. */
    public function httpRequest(): void
    {
        $this->handle($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET')->send();
    }

    public function handle(string $uri, string $method = 'GET'): Response
    {
        try {
            return $this->dispatch($uri, strtoupper($method));
        } catch (Throwable $e) {
            error_log((string) $e);

            return ErrorPage::response(500, $this->debug ? $e->getMessage() : '');
        }
    }

    private function dispatch(string $uri, string $method): Response
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if (!is_string($path)) {
            return ErrorPage::response(400);
        }

        $segments = array_values(array_filter(explode('/', $path), static fn (string $s): bool => $s !== ''));
        foreach ($segments as $segment) {
            if (!preg_match(self::SEGMENT_PATTERN, $segment)) {
                return ErrorPage::response(404);
            }
        }

        $query = parse_url($uri, PHP_URL_QUERY);
        $suffix = is_string($query) && $query !== '' ? '?' . $query : '';

        switch (count($segments)) {
            case 0:
                return Response::redirect('/' . $this->defaultController . '/Index' . $suffix);

            case 1:
                [$segment] = $segments;
                if ($this->resolveAction($this->defaultController, $segment) !== null) {
                    return Response::redirect('/' . $this->defaultController . '/' . $segment . $suffix);
                }
                if ($this->resolveAction($segment, 'index') !== null) {
                    return Response::redirect('/' . $segment . '/Index' . $suffix);
                }

                return ErrorPage::response(404);

            case 2:
                [$controllerName, $actionName] = $segments;
                $action = $this->resolveAction($controllerName, $actionName);
                if ($action === null) {
                    return ErrorPage::response(404);
                }
                if (!in_array($method, ['GET', 'HEAD', 'POST'], true)) {
                    return ErrorPage::response(405);
                }

                return $this->invoke($action);

            default:
                return ErrorPage::response(404);
        }
    }

    /**
     * Return the routable action method, or null when the controller or action
     * does not exist or is not allowed to be called from a URL.
     */
    private function resolveAction(string $controllerName, string $actionName): ?ReflectionMethod
    {
        $class = $this->controllerNamespace . ucfirst($controllerName) . 'Controller';
        if (!class_exists($class)) {
            return null;
        }

        $reflection = new ReflectionClass($class);
        if (!$reflection->isSubclassOf(Controller::class) || !$reflection->isInstantiable()) {
            return null;
        }
        if (!$reflection->hasMethod($actionName)) {
            return null;
        }

        $method = $reflection->getMethod($actionName);
        $declaredInFramework = $method->getDeclaringClass()->getName() === Controller::class;
        if (
            !$method->isPublic()
            || $method->isStatic()
            || $method->isConstructor()
            || str_starts_with($method->getName(), '__')
            || $declaredInFramework
            || $method->getNumberOfRequiredParameters() > 0
        ) {
            return null;
        }

        return $method;
    }

    private function invoke(ReflectionMethod $action): Response
    {
        $class = $action->getDeclaringClass();
        $name = preg_replace('/Controller$/', '', $class->getShortName());
        $controller = $class->newInstance($name, $action->getName());
        $result = $action->invoke($controller);

        return match (true) {
            $result instanceof Response => $result,
            is_string($result) => new Response($result),
            default => throw new \UnexpectedValueException(
                $class->getName() . '::' . $action->getName() . '() must return a Response or string.'
            ),
        };
    }
}
