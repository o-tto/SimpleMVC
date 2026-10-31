<?php

declare(strict_types=1);

namespace controller;

use mvcsystem\Controller;
use mvcsystem\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return $this->view();
    }

    public function features(): Response
    {
        return $this->view();
    }

    public function docs(): Response
    {
        return $this->view();
    }
}
