<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
<title>Demo Website</title>
<link rel="stylesheet" href="style.css">
<style>
    pre { width: 600px; height: 1000px; overflow-y: scroll; overflow-x: scroll; border: 1px solid gray; }
</style>
<body>
<div class="content">
    <div class="menu"><?php echo (new Menu())->render(); ?></div>

    <div>
        <h1>JSON data</h1>
        <p>
            this page demonstrates how to embed the listing of your
            projects listed at <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>
            into this demo-website.
        </p>
        <p>
            the other pages show how to get (pre-)rendered html from our
            API. this page shows how to receive a structured listing of your
            projects with all related information in JSON-format.
        </p>
        <p>
            you gain full access to all data-fields using this method.
            the downside is you need to render the project-listing yourself
            if you want to show it to the user.
        </p>
        <p>
            at this page we only show a list of the project-titles of
            the first 10 projects.
        </p>
        <p>
            this is a very powerful API. this simple example demonstrates only a
            basic usage scenario. consult the API-documentation to read
            about additional functionality the API provides.
        </p>

        <div>
            <?php
            $offset = array_key_exists('offset', $_REQUEST) ? filter_var($_REQUEST['offset'], FILTER_VALIDATE_INT) : null;

            $client = new LosysClient();
            $data = $client->callApi('api/customer/project', ['limit' => 10, 'offset' => $offset, 'expand' => 'language,project_images']);
            /*
             * you may provide filter-parameters to query only
             * selected projects. available parameters include
             * 'yearFrom', 'yearTo', 'projectIds', 'companyIds',
             * 'groupIds', 'languages', 'categoryIds', 'cantons',
             * 'status', 'visibility' or 'withImage'.
             *
             * use 'searchText' and 'searchTextIn' (which may be either
             * 'project', 'companies' or 'typeOfWork') for full-text-
             * searches. Optionally use 'concatenation' as 'and' or 'or'
             * here.
             *
             * to order the returned list use 'orderBy' which may be
             * an array of 0..x of 'yearOfCompletion', 'hasImage', 'id',
             * 'zipcode', 'city', 'priority' and 'canton'.
             *
             * hint:
             *   you may use /api/v1/project/filter_multiple_choice_values
             *   to see a list of all available values per filter-option
             *   (e.g. all 'canton'-s that your projects use).
             *
             * you can also provide an 'expand'-parameter asking
             * to include related entities in the response. it is
             * an array of one or more of the following values:
             *      'project_images',
             *      'project_videos',
             *      'parent',
             *      'children',
             *      'project_properties',
             *      'project_categories',
             *      'project_type_of_works',
             *      'project_type_of_constructions',
             *      'project_type_of_buildings',
             *      'project_address_contact_persons',
             *      'project_participating_companies',
             *      'company',
             *      'language',
             * instead of these '*' returns all available related
             * entities.
             *
             * use 'limit' and 'offset' to control how many dataset
             * starting at which offset will be returned.
             */

            // render the project-titles
            echo '<h3>custom rendering</h3>';
            echo '<table><tbody>';
            echo '<tr>'
                 . implode('', array_map(fn ($value) => '<th>' . htmlentities($value) . '</th>', [
                    'Title',
                    'Canton',
                    'Year',
                    '# images'
                 ]))
                 . '</tr>';
            foreach($data as $project)
                echo '<tr>'
                     . implode('', array_map(fn ($value) => '<td>' . htmlentities($value??'') . '</td>', [
                        $project['title'],
                        $project['canton'],
                        $project['yearOfCompletion'],
                        array_key_exists('project_images', $project) ? count($project['project_images']) : '???'
                     ]))
                     . '</tr>';
            echo '</tbody></table>';

            echo '<p><a href="?offset=' . (($offset ?? 0) + 10) . '">next page</a></p>';

            // show the received JSON
            echo '<h3>this is what the api really returns</h3>';
            echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</pre>';
            ?>
        </div>

        <p>
            this is a static footer-text on the test-website.
        </p>
    </div>
</div>
</body>
</html>
