<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; ?>
<html lang="en">
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
<body>
    <div class="content">
        <div class="menu"><?php echo (new Menu())->render(); ?></div>

        <div>
            <h1>Default settings</h1>
            <p>
                this page demonstrates how to embed the listing of all your
                projects listed at losys.ch into a demo-website.
            </p>
            <p>
                the following list of projects is rendered on the fly by the losys.ch
                backend and injected into this demo-website.
            </p>

            <div>
                <?php
                $client = new LosysClient();
                echo $client->callApi('api/customer/project/html/box', [], 'GET', 'text/html');
                ?>
            </div>

            <p>
                this is a static footer-text on our test-website.
            </p>
        </div>
    </div>
</body>
</html>
