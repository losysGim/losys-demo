<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
<head>
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">

    <!-- this is the regular Bootstrap-include from your own website -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" integrity="sha512-rt/SrQ4UNIaGfDyEXZtNcyWvQeOq0QLygHluFQcSjaGB04IxWhal71tKuzP6K8eYXYB6vJV4pHkXcmFGGQ1/0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="content">
    <div class="menu"><?php echo (new Menu())->render(); ?></div>

    <div>
        <h1>No Bootstrap</h1>
        <p>
            this page demonstrates how to embed the listing of your
            projects listed at <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>
            into this demo-website.
        </p>
        <p>
            the html that our backend creates uses common libraries like
            <a href="https://getbootstrap.com/docs/4.6/getting-started/introduction/">bootstrap v4</a>,
            <a href="https://developer.snapappointments.com/bootstrap-select/">Bootstrap-select v1.13</a> and
            <a href="https://jquery.com/download/">jQuery 3.6</a>.
        </p>
        <p>
            in the default-mode these libraries are loaded from inside the generated &lt;div&gt;.
            if you already use jQuery or Bootstrap in your own website this will cause the libraries to be
            loaded twice and thus create problems. therefor - if you for example jQuery and Bootstrap are
            already loaded by your own website - you can tell our API not to load these libraries again.
        </p>
        <p>
            in this example page we already included jQuery and Bootstrap in the &lt;head&gt; of the webpage. thus
            we instructed the Losys API not to include them again in the &lt;div&gt; with the project-listing
            using the 'skip_includes'-parameter.
        </p>

        <div>
            <?php
            $client = new LosysClient();
            echo $client->callApi('api/customer/project/html/box', ['skip_includes' => ['jquery', 'bootstrap']], 'GET', 'text/html');
            /*
            * available options for 'skip_includes' include
            * 'bootstrap', 'polyfill', 'jquery', 'popper', 'select2'
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
