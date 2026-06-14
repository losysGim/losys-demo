<?php
/**
 * shared helpers for the project-box demo scenarios.
 *
 * every scenario page reads the customized project-box link from config.json
 * (the link you receive from Losys support). the scenarios differ only in the
 * surrounding HTML/CSS/JS of the host page, so you can check that the project-box
 * behaves correctly inside different website setups (page-builders, classic CMS,
 * website builders, fixed-width layouts …).
 */

/** load the project-box link from config.json and fail loudly if not configured. */
function losys_link(): string
{
    $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true, 512, JSON_THROW_ON_ERROR);
    if (str_contains($config['link'], '(')) {
        throw new InvalidArgumentException('you must insert the customized URI to your project-box into the file config.json!');
    }
    return rtrim($config['link'], '/');
}

/**
 * the navigation shared by all scenario pages so you can jump between the
 * different host-page setups while visually comparing the embedded box.
 */
function losys_nav(string $current): void
{
    $items = [
        'index.php'              => 'Übersicht',
        'classic_cms.php'        => '1 · Klassisches CMS (iframe)',
        'wordpress_builder.php'  => '2 · WordPress + Page-Builder',
        'custom_grid.php'        => '3 · Custom / Bootstrap-Grid',
        'website_builder.php'    => '4 · Website-Baukasten',
        'legacy_fixed.php'       => '5 · Legacy + div/JS',
        'iframe.php'             => 'Basis: iframe',
        'div.php'                => 'Basis: div/JS',
    ];
    echo '<nav class="demo-switcher">';
    echo '<strong>Project-Box Demo-Szenarien:</strong> ';
    foreach ($items as $file => $label) {
        $active = ($file === $current) ? ' aria-current="page"' : '';
        echo '<a href="' . $file . '"' . $active . '>' . htmlspecialchars($label) . '</a>';
    }
    echo '</nav>';
}

/** small shared stylesheet for the demo-switcher bar (kept out of the scenario CSS on purpose). */
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
