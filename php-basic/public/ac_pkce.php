<?php
    require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php';
    use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu, Losys\CustomerApi\Client\RedirectException;

$error_head = $error_body = null;

    /**
     * this page demonstrates the use of the Authorization Code Flow
     *
     * beware:
     *   this flow is not available for client-credentials customer-tokens.
     *   it requires a "Portal"-type public client (no client_secret, PKCE),
     *   created on the Losys side via `php artisan create:portalClient`.
     *
     * for this to work...
     * ...these must be the very first lines in this .PHP file
     *    (not a single character before the opening tag of this block!)
     * ...you must not set LOSYS_CLIENT_SECRET in your .env file
     * ...you must set LOSYS_CLIENT_APP in your .env file to the base URI of
     *    this demo (the redirect target `<LOSYS_CLIENT_APP>ac_pkce.php` must be
     *    registered as a redirect_uri on your Portal client).
     * ...the redirect happens in the END-USER's browser (PKCE) — the Losys
     *    backend does NOT need to reach this page server-to-server.
     */
    try {
        $client = new LosysClient();
        if ($client->useAuthorizationCodeFlow()) {
            if (session_start()) {
                $client->setAuthorizationCodeFlowState($_GET['code'] ?? null, $_GET['state'] ?? null);
            } else {
                $error_head = 'PHP sessions not working';
                $error_body = '
                    to use this page/this flow, you must enable the use of
                    <a href="https://www.php.net/manual/en/book.session.php" target="_blank">PHP sessions</a>.
                ';
            }
        } else {
            $error_head = 'Not configured for Authorization Code Flow';
            $error_body = '
                    to use this page/this flow, you must configure your <pre>.env</pre>-file without setting
                    <pre>LOSYS_CLIENT_SECRET</pre> but with <pre>LOSYS_CLIENT_APP</pre> set to the base URI
                    of this demo-application. this URI must be publicly reachable (that is the Losys-backend
                    must be able to call this page via HTTP).
                ';
        }
    } catch(RedirectException $e) {
        header("Location: {$e->redirectToUri}");
        die();
    } catch(Throwable $e) {
        // escape: the message may contain attacker-influenced data (e.g. an
        // OAuth `error_description` reflected from the query) and is echoed
        // into the HTML below.
        $error_head = htmlspecialchars('Error ' . get_class($e), ENT_QUOTES, 'UTF-8');
        $error_body = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
?>
<html lang="en">
<head>
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content">
        <div class="menu"><?php echo (new Menu())->render(); ?></div>

        <div>
            <h1>Authorization Code Flow example</h1>
            <p>
                this page demonstrates authenticating against the API with the
                <a href="https://oauth.net/2/pkce/" target="_blank">OAuth Authorization Code Flow with PKCE</a>.
            </p><p>
                this flow is meant to be used by "public clients" which, in "OAuth"-vocabulary, are
                client-applications that are not able to safely store a secret. this is the case for all
                JavaScript-/Single-Page-Web-Applications as they have no means of storing secrets that
                are not exposed to the user controlling the browser in which the application runs. these
                applications <strong>must not</strong> use the "Client Credentials Flow" that is used in
                the authentication of the other pages in this example (e.g. "JSON data" or "Filters").
            </p>
<?php
            if ($error_head || $error_body) {
?>
                <p class="error">
                    <span class="head"><?php echo $error_head ?: 'error'; ?></span>
                    <?php echo $error_body ?: 'an error occurred'; ?>
                </p>
<?php
            } else {
?>
                <p>
                    You did it. The authorization worked.
                </p>
<?php
            }
?>
        </div>
    </div>
</body>