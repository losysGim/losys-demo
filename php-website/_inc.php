<?php
/**
 * shared helpers for the project-box demo pages.
 *
 * every page reads its settings from config.json (copy config.json.example and
 * insert the values you received from Losys support). the pages differ only in
 * the embedding method (iframe / HTML-tag / div+JS) and in the surrounding
 * HTML/CSS/JS of the host page, so you can check that the project-box behaves
 * correctly inside different website setups (page-builders, classic CMS,
 * website builders, fixed-width layouts …).
 */

/** load the demo configuration and fail loudly if the file is missing. */
function losys_config(): array
{
    $file = __DIR__ . '/config.json';
    if (!is_file($file)) {
        throw new RuntimeException('missing config.json - copy config.json.example to config.json and insert the values you received from Losys support!');
    }
    return json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
}

/** the personal link to your project-box (you receive it from Losys support). */
function losys_link(): string
{
    $config = losys_config();
    if (str_contains($config['link'] ?? '(', '(')) {
        throw new InvalidArgumentException('you must insert the customized URI to your project-box into the file config.json!');
    }
    return rtrim($config['link'], '/');
}

/** the script URL for the HTML-tag embedding (you receive it from Losys support together with your link). */
function losys_projectbox_script(): string
{
    $config = losys_config();
    if (str_contains($config['projectbox_script'] ?? '(', '(')) {
        throw new InvalidArgumentException('you must insert the script URL for the HTML-tag embedding into the file config.json!');
    }
    return $config['projectbox_script'];
}

/**
 * the navigation shared by all demo pages so you can jump between the
 * embedding methods and host-page setups while visually comparing the box.
 */
function losys_nav(string $current): void
{
    $items = [
        'index.php'                    => 'Übersicht',
        'iframe.php'                   => 'iframe · Basis',
        'iframe_classic_cms.php'       => 'iframe 1 · Klassisches CMS',
        'iframe_wordpress_builder.php' => 'iframe 2 · WordPress + Page-Builder',
        'iframe_custom_grid.php'       => 'iframe 3 · Custom / Bootstrap-Grid',
        'iframe_website_builder.php'   => 'iframe 4 · Website-Baukasten',
        'component.php'                => 'HTML-Tag · Basis',
        'component_styling.php'        => 'HTML-Tag · Gestaltung',
        'div.php'                      => 'div/JS · Basis',
        'div_legacy_fixed.php'         => 'div/JS 5 · Legacy',
    ];
    echo '<nav class="demo-switcher">';
    echo '<strong>Project-Box Demo:</strong> ';
    foreach ($items as $file => $label) {
        $active = ($file === $current) ? ' aria-current="page"' : '';
        echo '<a href="' . $file . '"' . $active . '>' . htmlspecialchars($label) . '</a>';
    }
    echo '</nav>';
}

/** small shared stylesheet for the demo-switcher bar (kept out of the page CSS on purpose). */
function losys_switcher_css(): void
{
    ?>
    <style>
        .demo-switcher { font: 13px/1.5 system-ui, sans-serif; background: #1f2933; color: #cbd2d9;
            padding: 8px 12px; display: flex; flex-wrap: wrap; gap: 4px 10px; align-items: center; }
        .demo-switcher strong { color: #fff; margin-right: 6px; }
        .demo-switcher a { color: #9fb3c8; text-decoration: none; padding: 2px 6px; border-radius: 4px; }
        .demo-switcher a:hover { background: #323f4b; color: #fff; }
        .demo-switcher a[aria-current="page"] { background: #2bb0ed; color: #04243a; font-weight: 600; }
        .demo-note { font: 13px/1.6 system-ui, sans-serif; background: #fff8e1; border: 1px solid #f0d27a;
            border-radius: 6px; padding: 10px 14px; margin: 16px 0; color: #5b4a1a; }
        .demo-note code { background: #00000010; padding: 1px 4px; border-radius: 3px; }
    </style>
    <?php
}
