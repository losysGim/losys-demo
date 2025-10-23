<!DOCTYPE html>
<html class="no-js" lang="de-DE" >
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <style>
            .top, .left, .content { padding: 1rem 1rem 1rem 1rem; margin: 0.5rem 0.5rem 0.5rem 0.5rem; }
            .column { flex-direction: column; }
            .row { flex-direction: row; }

            .top { background-color: aliceblue; min-height: 3rem; }
            .left { background-color: antiquewhite; }
            .content { background-color: lavender; width: 100%; flex-direction: column; }
            .container, .content { display: flex; }
        </style>
    </head>

    <body>
<?php
    $config = json_decode(file_get_contents('config.json'), true, 512, JSON_THROW_ON_ERROR);
    if (str_contains($config['link'], '('))
        throw new InvalidArgumentException('you must insert the customized URI to your project-box into the file config.json!');
?>
        <div class="container column">
            <div class="top">
                <h3>Top navigation</h3>
                this is the top-level navigation of your website
            </div>

            <div class="container row">
                <div class="left">
                    <h3>Navigation</h3>
                    <ul>
                        <li><strong>iFrame-Demo</strong></li>
                        <li><a href="div.php">Div-Demo</a></li>
                    </ul>
                </div>

                <div class="content">
                    <p>this is the content-section with your regular website-content.</p>
                    <p>we include the "Losys Project Box" below this text using an <strong>&lt;iframe&gt;</strong>-element.</p>

                    <iframe src="<?php echo $config['link']; ?>"></iframe>
                    <script src="<?php echo $config['link']; ?>/losys.js"></script>
                </div>
            </div>
        </div>
    </body>
</html>
