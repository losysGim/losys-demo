<?php use GuzzleHttp\Exception\GuzzleException, Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu, \League\OAuth2\Client\Provider\Exception\IdentityProviderException, Losys\CustomerApi\Client\LosysBackendException; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
<body>
    <div class="content">
        <div class="menu"><?php echo (new Menu())->render(); ?></div>

        <div>
            <h1>Default settings</h1>
            <p>
                this page demonstrates how to embed the listing of your
                projects listed at <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>
                into this demo-website.
            </p>
            <p>
                the following list of projects is rendered on the fly by the Losys
                backend and injected into this demo website.
            </p>
            <p>
                the language used in the project-listing is automatically adapted
                to the language you configured in the settings of your browser.
            </p>

            <div>
                <?php
                try
                {
                    // to see the error-handling in action try providing ['offset' => 'xx'] as $data-parameter to $client->callApi()
                    $client = new LosysClient();
                    echo $client->callApi('api/customer/project/html/box', [], 'GET', 'html');

                } catch (LosysBackendException $e) {
                     // indicates the Losys API returned an error
                    echo
                        "<p class='error'>\"{$e->getErrorType()}\"-Error with Code #{$e->getErrorCode()} from Losys-API: {$e->getMessage()}<br />"
                        . "If you need to contact Losys-Support on this error please provide your Request-Id \"{$e->getRequestId()}\"</p>";

                } catch (GuzzleException $e) {
                    // indicates there is a problem connecting to the server or a network-problem (e.g. no internet-access)
                    echo "<p class='error'>Network-/Transmission-Error: {$e->getMessage()}</p>";

                } catch (IdentityProviderException $e) {
                    // indicates there is an error authenticating against the Losys API
                    echo "<p class='error'>You could not be authenticated (check LOSYS_CLIENT_ID and LOSYS_CLIENT_SECRET in your .env-file): {$e->getMessage()}</p>";
                } catch (Throwable $e) {
                    echo "<p class='error'>Error: {$e->getMessage()}</p>";
                }
                ?>
            </div>

            <p>
                this is a static footer-text on the test-website.
            </p>
        </div>
    </div>
</body>
</html>
