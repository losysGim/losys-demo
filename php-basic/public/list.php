<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
<title>Demo Website</title>
<link rel="stylesheet" href="style.css">
<style>
    .projects .card-title { color: red; }
    .card-view { border: 5px solid blue; margin-bottom: 25px; }
    .card-view .indicators { display: none; }
</style>
<body>
<div class="content">
    <div class="menu"><?php echo (new Menu())->render(); ?></div>

    <div>
        <h1>Plain listing</h1>
        <p>
            this page demonstrates how to embed the listing of your
            projects listed at <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>
            into this demo-website.
        </p>
        <p>
            the default "project_box"-include shown on the other pages renders
            an interactive &lt;div&gt; that gives the user the possibility to
            search the list of projects.
        </p>
        <p>
            if you want a plain html-listing (that is not interactive) you
            can use the API-method presented here. in this example we ask
            it to only return projects from the year 2022 onwards.
        </p>
        <p>
            when using this api you must provide your own CSS to style the
            received listing. in this example we colored all the project-titles
            in red.
        </p>

        <div class="projects">
            <?php
            $client = new LosysClient();
            echo $client->callApi('api/customer/project/html/list', ['year_from' => 2022], 'GET', 'html');
            /*
             * available filter-parameters include
             * 'year_to', 'projectIds', 'companyIds', 'groupIds',
             * 'languages', 'categoryIds', 'cantons' or 'status'.
             *
             * if you provide 'searchText' you must additionally
             * provide 'searchTextIn' (with one or more of 'project',
             * 'companies' or 'typeOfWork').
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
