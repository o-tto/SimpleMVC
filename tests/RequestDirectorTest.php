<?php

declare(strict_types=1);

namespace tests;

use mvcsystem\RequestDirector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RequestDirectorTest extends TestCase
{
    private RequestDirector $app;

    protected function setUp(): void
    {
        $this->app = new RequestDirector();
    }

    public function testRootRedirectsToHomeIndex(): void
    {
        $response = $this->app->handle('/');

        self::assertSame(302, $response->status);
        self::assertSame('/Home/Index', $response->headers['Location']);
    }

    public function testSingleSegmentRedirectsToHomeAction(): void
    {
        $response = $this->app->handle('/Docs?x=1');

        self::assertSame(302, $response->status);
        self::assertSame('/Home/Docs?x=1', $response->headers['Location']);
    }

    #[DataProvider('homePages')]
    public function testHomePagesRenderWithLayout(string $uri, string $expected): void
    {
        $response = $this->app->handle($uri);

        self::assertSame(200, $response->status);
        self::assertStringContainsString('<title>SimpleMVC</title>', $response->body);
        self::assertStringContainsString($expected, $response->body);
    }

    /** @return iterable<string, array{string, string}> */
    public static function homePages(): iterable
    {
        yield 'index' => ['/Home/Index', 'Compact &amp; Fast MVC Framework'];
        yield 'lowercase url' => ['/home/index', 'Compact &amp; Fast MVC Framework'];
        yield 'features' => ['/Home/Features', '<h1>Features</h1>'];
        yield 'docs with query string' => ['/Home/Docs?page=2', '<h1>Docs</h1>'];
    }

    #[DataProvider('notFoundUris')]
    public function testUnknownOrForbiddenRoutesReturn404(string $uri): void
    {
        $response = $this->app->handle($uri);

        self::assertSame(404, $response->status);
        self::assertStringContainsString('404 - Not Found', $response->body);
    }

    /** @return iterable<string, array{string}> */
    public static function notFoundUris(): iterable
    {
        yield 'unknown controller' => ['/Nope/Index'];
        yield 'unknown action' => ['/Home/nope'];
        yield 'single unknown segment' => ['/nope'];
        yield 'too many segments' => ['/Home/Index/extra'];
        yield 'base controller helper' => ['/Home/view'];
        yield 'base controller redirect' => ['/Home/redirect'];
        yield 'constructor' => ['/Home/__construct'];
        yield 'encoded backslash' => ['/Home%5CController/index'];
        yield 'dot segment' => ['/../Home'];
    }

    public function testDisallowedMethodReturns405(): void
    {
        self::assertSame(405, $this->app->handle('/Home/Index', 'DELETE')->status);
    }

    public function testResponsesCarrySecurityHeaders(): void
    {
        $headers = $this->app->handle('/Home/Index')->headers;

        self::assertSame('nosniff', $headers['X-Content-Type-Options']);
        self::assertSame('SAMEORIGIN', $headers['X-Frame-Options']);
    }

    public function testOnlyPublicInstanceActionsWithoutArgumentsAreRoutable(): void
    {
        $app = new RequestDirector(controllerNamespace: 'tests\\Fixtures\\');

        self::assertSame('index', $app->handle('/Probe/index')->body);
        self::assertSame('plain', $app->handle('/Probe/plain')->body);
        self::assertSame(404, $app->handle('/Probe/hidden')->status);
        self::assertSame(404, $app->handle('/Probe/staticAction')->status);
        self::assertSame(404, $app->handle('/Probe/needsArgument')->status);
    }

    public function testExceptionsBecome500WithoutLeakingDetails(): void
    {
        $app = new RequestDirector(controllerNamespace: 'tests\\Fixtures\\');
        $response = $this->silenced(fn () => $app->handle('/Probe/boom'));

        self::assertSame(500, $response->status);
        self::assertStringNotContainsString('secret', $response->body);
    }

    public function testDebugModeShowsEscapedExceptionMessage(): void
    {
        $app = new RequestDirector(controllerNamespace: 'tests\\Fixtures\\', debug: true);
        $response = $this->silenced(fn () => $app->handle('/Probe/boom'));

        self::assertSame(500, $response->status);
        self::assertStringContainsString('secret &lt;details&gt;', $response->body);
    }

    /**
     * Run a callback with error_log() output discarded.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    private function silenced(callable $callback): mixed
    {
        $previous = ini_set('error_log', '/dev/null');
        try {
            return $callback();
        } finally {
            ini_set('error_log', (string) $previous);
        }
    }
}
