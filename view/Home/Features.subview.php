<h1>Features</h1>
<ul>
    <li><strong>Convention-based routing</strong> &mdash; <code>/Home/Docs</code> runs <code>HomeController::docs()</code>.</li>
    <li><strong>Safe by default</strong> &mdash; only public actions declared on your own controllers are routable, URL segments are validated, and security headers are sent with every response.</li>
    <li><strong>Layouts &amp; subviews</strong> &mdash; <code>*.subview.php</code> files are wrapped in the controller's <code>_layout.php</code>.</li>
    <li><strong>Response objects</strong> &mdash; actions return a <code>Response</code>, which keeps controllers easy to test.</li>
    <li><strong>No runtime dependencies</strong> &mdash; just PHP and Composer's autoloader.</li>
</ul>
