<?php use Losys\CustomerApi\Client\LosysClient, Losys\Demo\Menu; require __DIR__ . '/../vendor/autoload.php'; ?>
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
            this page demonstrates how to embed the listing of all your
            projects listed at losys.ch into a demo-website.
        </p>
        <p>
            the other pages show how to get (pre-)rendered html from our
            API. this page shows how to receive a structured listing of your
            projects with all related information in JSON-format.
        </p>
        <p>
            you can gain full access to all data-fields using this method,
            but you need to render the project-listing yourself if you want
            to show the projects to the user.
        </p>
        <p>
            at this page we only show a list of the project-titles of
            the first 10 projects.
        </p>
        <p>
            this is a very powerful API. this simple example is only a very
            basic usage scenario. consult the API-documentation to read
            about additional functionality that the API provides.
        </p>

        <div>
            <?php
            $client = new LosysClient();
            $data = $client->callApi('api/customer/project', ['limit' => 10, 'expand' => 'language,project_images']);
            /*
             * you may provide filter-parameters to query only
             * selected projects. available parameters include
             * 'year_from', 'year_to', 'projectIds', 'companyIds',
             * 'groupIds', 'languages', 'categoryIds', 'cantons'
             * or 'status'.
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
             * use 'limit' and 'start' to control how many dataset
             * starting at which offset will be returned.
             */

            // render the project-titles
            echo '<h3>this is custom rendering (only titles)</h3>';
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
                     . implode('', array_map(fn ($value) => '<td>' . htmlentities($value) . '</td>', [
                        $project['title'],
                        $project['canton'],
                        $project['yearOfCompletion'],
                        count($project['project_images'])
                     ]))
                     . '</tr>';
            echo '</tbody></table>';

            // show the received JSON
            echo '<h3>this is what the api really returns</h3>';
            echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</pre>';
            ?>
        </div>

        <p>
            this is a static footer-text on our test-website.
        </p>
    </div>
</div>
</body>
</html>
