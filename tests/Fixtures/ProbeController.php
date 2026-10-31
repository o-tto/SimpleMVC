<?php

declare(strict_types=1);

namespace tests\Fixtures;

use mvcsystem\Controller;
use mvcsystem\Response;

class ProbeController extends Controller
{
    public function index(): Response
    {
        return $this->text('index');
    }

    public function boom(): Response
    {
        throw new \RuntimeException('secret <details>');
    }

    public function plain(): string
    {
        return 'plain';
    }

    public function needsArgument(string $value): Response
    {
        return $this->text($value);
    }

    public static function staticAction(): Response
    {
        return Response::text('static');
    }

    protected function hidden(): Response
    {
        return $this->text('hidden');
    }
}
