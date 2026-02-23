<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu, Losys\Demo\ApiResultRenderer; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
<head>
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
</head>
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

            } catch (Throwable $e) {
                echo ApiResultRenderer::renderExceptionInfo($e);
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
