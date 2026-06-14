<?php require __DIR__ . '/_inc.php'; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project-Box – Demo-Szenarien</title>
    <?php losys_switcher_css(); ?>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, "Segoe UI", sans-serif; color: #1f2933; background: #f5f7fa; line-height: 1.55; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 32px 20px 64px; }
        h1 { font-size: 28px; }
        .lead { color: #52606d; }
        .card { display: block; background: #fff; border: 1px solid #e4e7eb; border-radius: 10px;
            padding: 18px 20px; margin: 14px 0; text-decoration: none; color: inherit; transition: box-shadow .15s, transform .15s; }
        .card:hover { box-shadow: 0 6px 18px #0000001a; transform: translateY(-2px); }
        .card h2 { margin: 0 0 4px; font-size: 18px; color: #2bb0ed; }
        .card .who { color: #7b8794; font-size: 13px; margin: 0 0 8px; }
        .card p { margin: 0; color: #3e4c59; font-size: 14px; }
        .basic { background: #eef1f4; }
    </style>
</head>
<body>
<?php losys_nav('index.php'); ?>
<div class="wrap">
    <h1>Project-Box: Einbettungs-Demo-Szenarien</h1>
    <p class="lead">
        Diese Seiten zeigen die Losys Project-Box, eingebettet in unterschiedlich aufgebaute Host-Webseiten,
        und helfen beim visuellen Testen der Integration in verschiedene Layout- und CMS-Situationen.
        Den Box-Link in <code>config.json</code> eintragen, dann
        <code>php -S 127.0.0.1:8006 -t php-website</code> starten und die Szenarien vergleichen.
    </p>

    <a class="card" href="classic_cms.php">
        <h2>1 · Klassisches CMS (iframe)</h2>
        <p class="who">z.&nbsp;B. TYPO3 · Joomla · Contao – serverseitig gerendert</p>
        <p>Zentrierte Content-Spalte fester Breite, Standard-iframe-Einbettung mit dem
           <code>losys.js</code>-Resizer.</p>
    </a>

    <a class="card" href="wordpress_builder.php">
        <h2>2 · WordPress + Page-Builder</h2>
        <p class="who">z.&nbsp;B. Divi · Elementor · WPBakery</p>
        <p>iframe in einem Page-Builder-Layout: Theme-CSS auf <code>iframe</code>, verschachtelte Wrapper
           und eine Einblend-Animation, bei der die Box beim Laden ausserhalb des sichtbaren Bereichs liegt.</p>
    </a>

    <a class="card" href="custom_grid.php">
        <h2>3 · Custom / Bootstrap-Grid</h2>
        <p class="who">Bootstrap 5 · CSS-Grid · Swiper · sticky Header</p>
        <p>Box in einer CSS-Grid-Spalte (mit <code>min-width:0</code>), sticky Header mit z-index und
           einem Slider darüber.</p>
    </a>

    <a class="card" href="website_builder.php">
        <h2>4 · Website-Baukasten</h2>
        <p class="who">z.&nbsp;B. Wix · Squarespace · Jimdo</p>
        <p>iframe in einem HTML-Embed-Widget mit fester Höhe – der Inhalt wird auf die Widget-Höhe
           begrenzt, ein Auto-Resize ist hier nicht möglich.</p>
    </a>

    <a class="card" href="legacy_fixed.php">
        <h2>5 · Legacy + div/JS</h2>
        <p class="who">Seite mit fester Breite (960&nbsp;px) · jQuery 3.x</p>
        <p>Box per jQuery <code>.load()</code> in ein <code>&lt;div&gt;</code> – generische
           Seiten-CSS-Klassen können in die Box durchschlagen. Hinweis: die div/JS-Einbettung benötigt
           jQuery&nbsp;≥&nbsp;3.</p>
    </a>

    <a class="card basic" href="iframe.php">
        <h2>Basis: iframe</h2>
        <p>Minimal-Beispiel der iframe-Einbettung (Ausgangspunkt).</p>
    </a>
    <a class="card basic" href="div.php">
        <h2>Basis: div/JS</h2>
        <p>Minimal-Beispiel der div/JavaScript-Einbettung (Ausgangspunkt).</p>
    </a>
</div>
</body>
</html>
