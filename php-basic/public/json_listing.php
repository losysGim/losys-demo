<?php use Losys\Demo\Menu, Losys\Demo\ApiResultRenderer; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
    <body class="full_width">
        <div class="content">
            <div class="menu"><?php echo new Menu()->render(); ?></div>

            <div class="content_col grow">
                <h1>JSON data</h1>
                <p>
                    this page demonstrates how to embed the listing of your projects listed at
                    <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a> into this demo-website.
                </p><p>
                    the other pages show how to get (pre-)rendered html from our API. this page shows how to receive
                    a structured listing of your projects with all related information in JSON-format.
                </p><p>
                    you gain full access to all data-fields using this method. the downside is you need to render
                    the project-listing yourself if you want to show it to the user.
                </p><p>
                    you must gather the project-list in chunks (page by page). use the "next page" and "previous page"
                    links to scroll through the results.
                </p><p>
                    this is a very powerful API. this simple example demonstrates only a basic usage scenario. consult
                    the API-documentation to read about additional functionality the API provides.
                </p>

                <div class="content_col grow">
                    <?php
                    $start = array_key_exists('start', $_REQUEST)
                        ? filter_var($_REQUEST['start'], FILTER_VALIDATE_INT)
                        : null;

                    /*
                     * if you page through the results you must make sure, that you provide
                     * exactly the same input-parameters with all requests (except the 'start'-
                     * and 'limit'-parameters which may of course differ). otherwise the returned
                     * results are invalid.
                     *
                     * to help you ensure that the API sends a 'X-Paginator-Hash'-header with
                     * every response you get. if send this header back with the request to
                     * gather the next page of results the API ensures that your input-parameters
                     * did not change. that makes the results way more reliable and is therefor
                     * strongly recommended.
                     */
                    echo new ApiResultRenderer()->getProjectsFromApiAndRenderResults(
                        [
                            'limit' => 25,
                            'start' => $start,
                            'expand' => 'language,project_images'
                        ],
                        array_key_exists('hash', $_REQUEST) && ($hash = $_REQUEST['hash'])
                            ? ['headers' => ['X-Paginator-Hash' => $hash]]
                            : [],
                        function() {
                            $stats = $this->client->getLastResponseStatistics();

                            if (!array_key_exists('has-next-page', $stats))
                            {
                                $result = 'This request was not paginated.';
                            } else {
                                $result =
                                        '<span>showing datasets '
                                        . ($stats['start'] + 1)
                                        . ' - '
                                        . ($stats['start'] + $stats['count'])
                                        . " (of {$stats['total']})</span>";

                                if ($stats['has-previous-page'])
                                    $result =
                                            '<span style="padding-right: 20px;"><a href="?' . http_build_query(['start' => $stats['start'] - $stats['count'], 'hash' => $stats['hash']]) . '">previous page</a></span>'
                                            . $result;
                                if ($stats['has-next-page'])
                                    $result .= '<span style="padding-left: 20px;"><a href="?' . http_build_query(['start' => $stats['start'] + $stats['count'], 'hash' => $stats['hash']]) . '">next page</a></span>';
                            }

                            return "<p class='paginator'>{$result}</p>";
                        }
                    );

                    /*
                     * you may provide filter-parameters to query only selected projects. available parameters include
                     * 'yearFrom', 'yearTo', 'projectIds', 'companyIds',  'groupIds', 'languages', 'categoryIds',
                     * 'cantons', 'status', 'visibility' or 'withImage'.
                     *
                     * use 'searchText' and 'searchTextIn' (which may be either 'project', 'companies' or 'typeOfWork')
                     * for full-text-searches. Optionally use 'concatenation' as 'and' or 'or' here.
                     *
                     * to order the returned list use 'orderBy' which may be an array of 0..x of 'yearOfCompletion',
                     * 'hasImage', 'id', 'zipcode', 'city', 'priority' and 'canton'.
                     *
                     * hint:
                     *   you may use /api/customer/project/filter_multiple_choice_values to see a list of all available
                     *   values per filter-option (e.g. all 'canton'-s that your projects use).
                     *
                     * you can also provide an 'expand'-parameter asking to include related entities in the response.
                     * it is an array of one or more of the following values:
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
                     * instead of these '*' returns all available related entities.
                     *
                     * use 'limit' and 'offset' to control how many dataset starting at which offset will be returned.
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
