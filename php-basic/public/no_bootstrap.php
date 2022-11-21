<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; ?>
<html lang="en">
<title>Demo Website</title>
<link rel="stylesheet" href="style.css">

<!-- this is the regular Bootstrap-include from your own website -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" integrity="sha512-rt/SrQ4UNIaGfDyEXZtNcyWvQeOq0QLygHluFQcSjaGB04IxWhal71tKuzP6K8eYXYB6vJV4pHkXcmFGGQ1/0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<body>
<div class="content">
    <div class="menu"><?php echo (new Menu())->render(); ?></div>

    <div>
        <h1>No Bootstrap</h1>
        <p>
            this page demonstrates how to embed the listing of all your
            projects listed at losys.ch into a demo-website.
        </p>
        <p>
            the losys website included here uses <a href="https://getbootstrap.com/docs/4.6/getting-started/introduction/">bootstrap v4</a>,
            <a href="https://developer.snapappointments.com/bootstrap-select/">Bootstrap-select v1.13</a> and
            <a href="https://jquery.com/download/">jQuery 3.6</a>.
            in the default-mode we do automatically include these required libraries in the loaded &lt;div&gt;.
        </p>
        <p>
            if you already use jQuery or Bootstrap in your own website it will cause problems if the libraries
            are loaded twice. therefor - if you make sure jQuery and Bootstrap are loaded in your website - you
            can tell our API not to include these libraries.
        </p>
        <p>
            in this example-page we did include jQuery and Bootstrap in the &lt;head&gt; of the page and did
            not include them again in the &lt;div&gt; with the project-listing using the 'skip_includes'-parameter.
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
            this is a static footer-text on our test-website.
        </p>
    </div>
</div>
</body>
</html>
