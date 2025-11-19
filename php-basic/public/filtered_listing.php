<?php use Losys\Demo\Utils, Losys\Demo\Menu, Losys\Demo\ApiResultRenderer; require __DIR__ . '/../vendor/autoload.php'; require __DIR__ . '/../src/base.php'; ?>
<html lang="en">
    <title>Demo Website</title>
    <link rel="stylesheet" href="style.css">
    <body class="full_width">
        <div class="content">
            <div class="menu"><?php echo new Menu()->render(); ?></div>

            <div class="content_col grow">
                <h1>Filters</h1>
                <p>
                    this page demonstrates how to utilize filters to search your projects listed at
                    <a href="https://www.referenz-verwaltung.ch">referenz-verwaltung.ch</a>.
                </p>

                <div class="content_col grow">
                    <?php
                    $renderer = new ApiResultRenderer();

                    if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
                        // show filter

                        /*
                         * this call gets all available values the user may use to filter project-listing.
                         * you may use the result to build a multiple-choice filter-ui.
                         *
                         * if you wish you may already filter these "filter-multiple-choice-values" by the
                         * same criteria that are accepted to filter the project-listing, e.g. you may send
                         *     {companyId: [1, 2, 3]}
                         * to show only the filter-values that existing in one of the companies 1, 2 or 3.
                         */
                        $filter = $renderer->client->callApi('/api/customer/project/filter_multiple_choice_values');

                        echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

                        /*
                         * all type-of-xxx with the same localized title over all companies
                         * that you have access to are grouped into one entry.
                         *
                         * example:
                         *   group-company A has type-of-work #400 with title.de = 'aaa' and title.fr = 'bbb'
                         *   group-company B has type-of-work #500 with title.de = 'bbb' and title.fr = 'ccc'
                         *
                         * if your language is 'de' you receive
                         *         "bbb": {
                         *             "ids": [
                         *                 400, 500
                         *             ],
                         *             "titles": {
                         *                 "bbb"
                         *             }
                         *         }
                         */
                        echo '<label for="typeOfWorkIds">Type Of Work</label>'
                            . '<select name="filter_typeOfWorkIds[]" id="typeOfWorkIds" multiple>'
                            . '<option value="">(all)</option>'
                            . implode('', array_map(fn($typeOfWork) => '<option value="' . htmlentities(json_encode($typeOfWork['ids'])) . '">'
                                . htmlentities($typeOfWork['titles']['displayLocale'])
                                . '</option>',
                                $filter['typeOfWork']
                            ))
                            . '</select><hr>';

                        /*
                         * Project-Attributes with the same localized title over all
                         * companies that you have access to are grouped into one entry.
                         * example:
                         *     "Beschreibung": {
                         *         "title": "Beschreibung",
                         *         "type": "textarea",
                         *         "subtype": null,
                         *         "ids": [
                         *             788,
                         *             795,
                         *             889,
                         *             817
                         *         ]
                         *     }
                         *
                         * available types: 'textarea', 'text', 'bool' or 'employees'
                         */
                        if (array_key_exists('attributeByName', $filter))
                            foreach($filter['attributeByName'] as $value)
                            {
                                $field = 'attributes_' . implode('_', $value['ids']);

                                switch ($value['type']) {
                                    case 'bool':
                                        echo
                                            '<label for="' . $field . '">' . htmlentities($value['title']) . '</label>'
                                            . '<select name="filter_' . $field . '[]" id="' . $field . '">'
                                            . '<option value="">(all)</option>'
                                            . '<option value="' . htmlentities(json_encode('True')) . '">yes</option>'
                                            . '<option value="' . htmlentities(json_encode('False')) . '">no</option>'
                                            . '</select><hr>';
                                    // other types not implemented in this demo
                                }
                            }

                        echo '<button type="submit">Search</button>';

                        echo '</form>';
                    } else {
                        // show results
                        $filter_values = Utils::convertUiFilterValues();

                        /*
                         * this is just an excerpt of the available filter-values. more are available.
                         * the existence of some may depend on your tenant-configuration.
                         *
                         * valid filter values for...
                         *    ...typeOfConstructionIds
                         *    ...typeOfBuildingIds
                         *    ...typeOfWorkIds
                         *    ...categoryIds
                         *      array of integers (IDs)
                         *
                         *    ...attributes
                         *      [['id' => {ids}, 'value' => {values}], ['name' => {names}, 'value' => {values}], ...]
                         *      {ids}    := int[]
                         *      {names}  := string[]
                         *      beware: either {ids} or {names} must be present, but never both.
                         *
                         *      {values} := [{value}, {value}, ...]
                         *      {value}  := either...
                         *                  ...1 or 'test'
                         *                  ...['from' => 4000, 'to' => 5000]
                         *                  ...['from' => 4000]
                         *                  ...['to' => 4000]
                         *                  ...['like' => 'search text' ]
                         *                  ...['contains' => 'search text']
                         *      example:
                         *          [
                         *              [ 'id' => [34, 36], 'value' => 1 ],
                         *              [ 'id' => 40, 'value' => [1, 'test'] ],
                         *              [ 'id' => [40], 'value' => ['from' => 4000, 'to' => 5000],
                         *              [ 'name' => ['test'], 'value' => 'huhu' ]
                         *          ]
                         */

                        echo $renderer->getProjectsFromApiAndRenderResults(
                            array_merge(
                                $filter_values,
                                [
                                    'limit' => 15,
                                    'expand' => 'project_properties,project_type_of_works,company'
                                ]
                            )
                        );
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
