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
                         * to show only the filter-values that exist in one of the companies 1, 2 or 3.
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
                         * available 'type's:
                         * 'textarea', 'text', 'bool', 'dropdown', 'number' or 'employees'
                         */
                        if (array_key_exists('attributeByName', $filter))
                            foreach($filter['attributeByName'] as $value)
                            {
                                $field = 'attributes_' . implode('_', $value['ids']);

                                switch($value['type']) {
                                    case 'employees':
                                        echo
                                            '<label for="' . $field . '">' . htmlentities($value['title']) . '</label>'
                                            . '<select name="filter_' . $field . '[]" id="' . $field . '" multiple>'
                                            . '<option value="">(all)</option>'
                                            . implode('', array_map(
                                                    fn(array $employee) => '<option value="' . htmlentities(json_encode($employee['id'])) . "\">{$employee['firstName']} {$employee['lastName']}</option>",
                                                    $value['employees']
                                            ))
                                            . '</select><hr>';
                                        break;

                                    case 'text':
                                    case 'textarea':
                                        echo
                                            '<label for="' . $field . '">' . htmlentities($value['title']) . '</label>'
                                            . '<input type="text" name="filter_' . $field . '" id="' . $field . '">'
                                            . '<hr>';
                                        break;

                                    case 'bool':
                                        echo
                                            '<label for="' . $field . '">' . htmlentities($value['title']) . '</label>'
                                            . '<select name="filter_' . $field . '[]" id="' . $field . '">'
                                            . '<option value="">(all)</option>'
                                            . '<option value="' . htmlentities(json_encode('True')) . '">yes</option>'
                                            . '<option value="' . htmlentities(json_encode('False')) . '">no</option>'
                                            . '</select><hr>';
                                        break;
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
                         *      [
                         *          [{selector}, {value_filter}],
                         *          [{selector}, {value_filter}],
                         *          [{selector}, {value_filter}],
                         *          ...
                         *      ]
                         *
                         *      ...with...
                         *
                         *      {selector}           := defines on which Attributes the {value_filter} is applied.
                         *                              ['id' => [123, 456, ...]]
                         *                                  filters based on Attribute.id
                         *                              or
                         *                              ['name' => ['abc', 'def', ...]]
                         *                                  filters based on the localized name of the Attribute.
                         *                                  this selects all Attributes having the localized name
                         *                                  in any(!) defined language matching the given text (using
                         *                                  the Attributes defined in all companies that you have
                         *                                  read-access to).
                         *
                         *                              either 'id' or 'name' must be present, but never both.
                         *
                         *      {value_filter}       := ['value' => {value_criterium}]
                         *                                  selects projects based on the value that the selected
                         *                                  Attributes are assigned in the searched projects.
                         *                                  a {value_criterium} always implies ['assigned' => true]
                         *                                  that is only matches projects that have any value assigned
                         *                                  for the selected Attributes.
                         *                              or
                         *                              ['assigned' => {assigned_criterium}]
                         *                                  selects projects that have the selected Attributes assigned
                         *                                  (no matter which value is assigned).
                         *                                  remember:
                         *                                      projects never have "empty" Attribute-values assigned.
                         *                                      if you set the value for an Attribute to empty in a
                         *                                      project the attribute-assignment is removed from the
                         *                                      project.
                         *
                         *                              either 'value' or 'assigned' must be present, but never both.
                         *
                         *      {assigned_criterium} := true | false
                         *
                         *      {value_criterium}    := usable criteria depend on the type of the attribute(s)
                         *                              that are selected:
                         *
                         *                              criterium               applicable Attribute-types
                         *                              {conjunction}           all
                         *                              {equals}                all
                         *                              {range}                 number
                         *                              {text_filter}           text, textarea
                         *
                         *      {conjunction}        := ['and' => [{value_filter}, {value_filter}, ...]]
                         *                              or
                         *                              ['or' => [{value_filter}, {value_filter}, ...]]
                         *                              or
                         *                              ['not' => [{value_filter}]]
                         *                              beware:
                         *                                  {value_filter} may not be an 'assigned' criterium here
                         *                                  (only 'value' is allowed).
                         *
                         *      {equals}             := [{value}, {value}, ...]
                         *                              {value} may be integer, float, boolean or string
                         *                              depending on the type of Attribute.
                         *                              beware:
                         *                                  for attribute.type='employees'
                         *                                      {value} may only be
                         *                                      integer   (matching the employee-id)
                         *                                      or string (matching either 'firstName lastName'
                         *                                                 or the 'email-address' if it contains a '@').
                         *                                      if you query the Attributes see the field 'employees'
                         *                                      for a list of available employees.
                         *
                         *                                  for attribute.type='dropdown'
                         *                                      {value} may only be
                         *                                      integer   (matching the value-id)
                         *                                      or string (matching the dropdown-value in any(!) language).
                         *                                      if you query the Attributes see the field 'selectable_values'
                         *                                      for a list of available dropdown-values.
                         *
                         *                                  for attribute.type='number'
                         *                                      {value} may be an integer, float or a string that is
                         *                                      convertible to integer or float with '.' being the
                         *                                      decimal-point (e.g. '123' or '123.56').
                         *
                         *                                  for attribute.type='bool'
                         *                                      {value} may only be the string(!) 'True' or 'False'
                         *
                         *                                  for attribute.type='text' or 'textarea'
                         *                                      {value} may only be the string that must exactly
                         *                                      match the value assigned to the Attribute in the project.
                         *
                         *      {range}              := ['from' => {value}, 'to' => {value}]
                         *                              or
                         *                              ['from' => {value}]
                         *                              or
                         *                              ['to' => {value}]
                         *
                         *                              {value} may be an integer, float or a string that is
                         *                              convertible to integer or float with '.' being the
                         *                              decimal-point (e.g. '123' or '123.56').
                         *                              if 'from' and 'to' are both provided and 'from' is bigger
                         *                              than 'to' they are automatically swapped.
                         *
                         *      {text_filter}        := ['like' => [{value}, {value}, ...]]
                         *                                  {value} must be a string.
                         *                                  searches for assigned Attribute-values that match any of the
                         *                                  given MySQL-SQL-LIKE pattern provided as {value}s.
                         *                                  Allowed wildcards are '*' (matching 0..x characters) and
                         *                                  '_' (matching exactly one character).
                         *                                  see https://dev.mysql.com/doc/refman/8.4/en/pattern-matching.html
                         *                              or
                         *                              ['contains' => [{value}, {value}, ...]]
                         *                                  {value} must be a string.
                         *                                  searches for assigned Attribute-values that contain any
                         *                                  of the given values. the search is performed case-insensitive
                         *                                  (that is 'Hallo' matching 'hallo').
                         *
                         *      if {selector} or {value_filter} are not present, the filter is silently ignored.
                         *
                         *      examples:
                         *          [
                         *              [ 'id' => [34, 36], 'value' => [1] ],
                         *              [ 'id' => [40], 'value' => [1, 'test'] ],
                         *              [ 'id' => [40], 'value' => ['from' => 4000, 'to' => 5000] ],
                         *              [ 'name' => ['test'], 'value' => ['huhu'] ],
                         *              [ 'id' => [40], 'value' => ['assigned' => true] ],
                         *              [ 'id' => [40], 'value' => ['not' => ['value' => 'test'] ],
                         *              [ 'name' => ['Projektleiter'], 'value' => ['hans@test.ch'] ],
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
