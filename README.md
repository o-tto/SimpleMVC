# SimpleMVC

A compact PHP implementation of the MVC pattern with no runtime dependencies.

## Requirements
- [PHP 8.3](https://www.php.net/supported-versions.php) or higher
- [Composer](https://getcomposer.org/)
- A web server that can rewrite URLs (Apache, Nginx, Caddy, IIS), or PHP's built-in server for development

## Getting started
```bash
composer install
composer serve   # php -S localhost:8000 -t public public/index.php
```
Then open <http://localhost:8000>.

In production, point the web server's document root at `public/`, so that
source files, `vendor/` and `composer.json` are never reachable over HTTP.
`public/.htaccess` handles URL rewriting for Apache. For Nginx:

```nginx
root /path/to/SimpleMVC/public;
location / {
    try_files $uri /index.php$is_args$args;
}
```

## Project layout
| Path          | Purpose                                              |
|---------------|------------------------------------------------------|
| `public/`     | Web root; `index.php` is the front controller        |
| `controller/` | Your controllers (`HomeController`, …)                |
| `view/`       | Views per controller, plus `_error/` pages            |
| `model/`      | Domain models                                         |
| `logic/`      | Business logic / services                             |
| `mvcsystem/`  | The framework itself                                  |

## Routing
`/{Controller}/{action}` runs `controller\{Controller}Controller::{action}()`.

- `/` redirects to `/Home/Index`.
- `/Docs` redirects to `/Home/Docs`; `/Order` redirects to `/Order/Index`.
- Only public, non-static methods without required parameters that are declared
  in your own controller are routable. Methods of the `mvcsystem\Controller`
  base class never are.
- Unknown routes return a real `404`, and uncaught exceptions return a `500`.

## Controllers
```php
<?php

namespace controller;

use mvcsystem\Controller;
use mvcsystem\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        return $this->view(['orders' => []]);  // view/Order/Index(.subview).php
    }
}
```

Helpers: `view()`, `redirect()`, `text()`, `json()` and `error()`.

## Views
- `view/{Controller}/{Action}.php` is rendered as a complete page.
- `view/{Controller}/{Action}.subview.php` is wrapped in `view/{Controller}/_layout.php` and passed to it as `$content`.
- View file names are matched case-insensitively.
- Escape output with `<?= e($value) ?>`.

## Debugging
Set `APP_DEBUG=1` to include exception messages on error pages. Never enable this in production.

## Tests
```bash
composer test
```

## License
Apache License 2.0
