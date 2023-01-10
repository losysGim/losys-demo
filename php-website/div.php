<!DOCTYPE html>
<html class="no-js" lang="de-DE" >
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <style>
            .my-top, .my-left, .my-content { padding: 1rem 1rem 1rem 1rem; margin: 0.5rem 0.5rem 0.5rem 0.5rem; }
            .my-container { display: flex; }
            .my-column { flex-direction: column; }
            .my-row { flex-direction: row; }

            .my-top { background-color: aliceblue; min-height: 3rem; }
            .my-left { background-color: antiquewhite; }
            .my-content { background-color: lavender; flex-grow: 2; }

            #losys #project-search input[name="searchText"] { background-color: red; }
        </style>

        <!-- this is the regular Bootstrap-include from your own website -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>

    <body>
<?php
    $config = json_decode(file_get_contents('config.json'), true, 512, JSON_THROW_ON_ERROR);
    if (strpos($config['link'], '(') !== false)
        throw new InvalidArgumentException('you must insert the customized URI to your project-box into the file config.json!');
?>
        <div class="my-container my-column">
            <div class="my-top">
                <h3>Top navigation</h3>
                this is the top-level navigation of your website
            </div>

            <div class="my-container my-row">
                <div class="my-left">
                    <h3>Navigation</h3>
                    <ul>
                        <li><a href="iframe.php">iFrame-Demo</a></li>
                        <li><strong>Div-Demo</strong></li>
                    </ul>
                </div>

                <div class="my-content">
                    <p>this is the content-section with your regular website-content.</p>
                    <p>we include the "Losys Project-Box" below this text using a <strong>&lt;div&gt;</strong>-element.</p>

                    <p>
                        you need to include <a target="_blank" href="https://jquery.com/">jQuery</a> included (at least in this page)
                        of your website. the reason is that we bring along our own javascript-files inside our
                        Project-Box. these have to be executed after downloading the Project-Box. while this is
                        possible in plain JavaScript (without jQuery) it would still be quiet some work. jQuery simplfies
                        that with its .load()-function
                    </p>
                    <p>
                        <strong>Hint:</strong>&nbsp;
                        we must take care not to use css-classnames that conflict with the ones from the Project-Box
                        in this scenario. Note how we prefixed the css-classnames in this page with "my-" to avoid
                        these conflicts.
                    </p>

                    <p>
                        <strong>Hint:</strong>&nbsp;
                        we can easily change the style of the project-box using css-classes in our own website. notice
                        how the "search"-textbox is made red here!
                    </p>

                    <!-- this div is used to inject the project-box into it -->
                    <div id="losys"></div>
                </div>
            </div>
        </div>

        <!-- this is the JavaScript that loads the project-box -->
        <script>
            $(document).ready(() => {
                $('#losys').load("<?php echo $config['link'] . (substr($config['link'], -1) == '/' ? '' : '/') . 'box?skip_includes[]=jquery'; ?>");
            });
        </script>
    </body>
</html>
