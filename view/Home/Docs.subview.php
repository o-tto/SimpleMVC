<h1>Docs</h1>

<h3>Controllers</h3>
<p>Create <code>controller/OrderController.php</code>:</p>
<pre><code><?= e(<<<'PHP'
<?php

namespace controller;

use mvcsystem\Controller;
use mvcsystem\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        return $this->view(['orders' => []]);
    }
}
PHP) ?></code></pre>
<p>Every public, non-static method without required parameters is an action, reachable at <code>/Order/{action}</code>.</p>

<h3>Responses</h3>
<ul>
    <li><code>$this-&gt;view($vars, $viewName = null, $status = 200)</code> &mdash; render a view (defaults to the action name).</li>
    <li><code>$this-&gt;redirect($url)</code>, <code>$this-&gt;text($body)</code>, <code>$this-&gt;json($data)</code></li>
    <li><code>$this-&gt;error(404)</code> &mdash; render <code>view/_error/404.php</code>.</li>
</ul>

<h3>Views</h3>
<p>Views live in <code>view/{Controller}/</code>. <code>Index.php</code> is rendered as a full page;
    <code>Index.subview.php</code> is wrapped in <code>_layout.php</code> and exposed there as <code>$content</code>.
    Always escape output with <code>&lt;?= e($value) ?&gt;</code>.</p>

<h3>Debugging</h3>
<p>Set the environment variable <code>APP_DEBUG=1</code> to show exception messages on error pages. Never enable this in production.</p>
