<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
<head>
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="content">
    <div class="menu"><?php echo (new Menu())->render(); ?></div>

    <div>
        <h1>No search-box</h1>
        <p>
            this page demonstrates how to embed the listing of your
            projects listed at <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>
            into this demo-website.
        </p>
        <p>
            the default search-box is not rendered here.
        </p>

        <div>
            <?php
            $client = new LosysClient();
            echo $client->callApi('api/customer/project/html/box', ['hide' => ['search']], 'GET', 'text/html');
            /*
             * available options for 'hide' include
             * 'search', 'map', 'pdf'
             */
            ?>
        </div>

        <p>
            this is a static footer-text on the test-website.
        </p>
    </div>
</div>
</body>
</html>
