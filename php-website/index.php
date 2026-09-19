<?php require __DIR__ . '/_inc.php'; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project-Box – Demo</title>
    <?php losys_switcher_css(); ?>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, "Segoe UI", sans-serif; color: #1f2933; background: #f5f7fa; line-height: 1.55; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 32px 20px 64px; }
        h1 { font-size: 28px; }
        h2 { font-size: 20px; margin: 36px 0 6px; }
        .lead, .group-lead { color: #52606d; }
        .group-lead { margin: 0 0 14px; font-size: 14px; }
        .card { display: block; background: #fff; border: 1px solid #e4e7eb; border-radius: 10px;
            padding: 18px 20px; margin: 14px 0; text-decoration: none; color: inherit; transition: box-shadow .15s, transform .15s; }
        .card:hover { box-shadow: 0 6px 18px #0000001a; transform: translateY(-2px); }
        .card h3 { margin: 0 0 4px; font-size: 18px; color: #2bb0ed; }
        .card .who { color: #7b8794; font-size: 13px; margin: 0 0 8px; }
        .card p { margin: 0; color: #3e4c59; font-size: 14px; }
        .basic { background: #eef1f4; }
    </style>
</head>
<body>
<?php losys_nav('index.php'); ?>
<div class="wrap">
    <h1>Project-Box: Einbindungs-Demo</h1>
    <p class="lead">
        Die Project-Box ist ein fertiges Widget von Losys, das die Projektreferenzen Ihres
        Unternehmens auf Ihrer eigenen Webseite anzeigt. Diese Demo zeigt die verfügbaren
        Einbindungswege und hilft beim visuellen Testen in verschiedenen Layout- und CMS-Situationen.
        Zum Starten <code>config.json.example</code> nach <code>config.json</code> kopieren, die
        Werte von Losys eintragen und <code>php -S 127.0.0.1:8006 -t php-website</code> ausführen.
    </p>

    <h2>Einbindung per iframe</h2>
    <p class="group-lead">
        Der empfohlene Standardweg: zwei Zeilen HTML, funktioniert in praktisch jeder Webseite.
        Die Szenarien zeigen die Box in unterschiedlich aufgebauten Host-Seiten.
    </p>

    <a class="card basic" href="iframe.php">
        <h3>Basis: iframe</h3>
        <p>Minimal-Beispiel der iframe-Einbindung mit dem <code>losys.js</code>-Höhenabgleich
           (Ausgangspunkt).</p>
    </a>

    <a class="card" href="iframe_classic_cms.php">
        <h3>1 · Klassisches CMS</h3>
        <p class="who">z.&nbsp;B. TYPO3 · Joomla · Contao – serverseitig gerendert</p>
        <p>Zentrierte Content-Spalte fester Breite, Standard-iframe-Einbettung mit dem
           <code>losys.js</code>-Resizer.</p>
    </a>

    <a class="card" href="iframe_wordpress_builder.php">
        <h3>2 · WordPress + Page-Builder</h3>
        <p class="who">z.&nbsp;B. Divi · Elementor · WPBakery</p>
        <p>iframe in einem Page-Builder-Layout: Theme-CSS auf <code>iframe</code>, verschachtelte Wrapper
           und eine Einblend-Animation, bei der die Box beim Laden ausserhalb des sichtbaren Bereichs liegt.</p>
    </a>

    <a class="card" href="iframe_custom_grid.php">
        <h3>3 · Custom / Bootstrap-Grid</h3>
        <p class="who">Bootstrap 5 · CSS-Grid · Swiper · sticky Header</p>
        <p>Box in einer CSS-Grid-Spalte (mit <code>min-width:0</code>), sticky Header mit z-index und
           einem Slider darüber.</p>
    </a>

    <a class="card" href="iframe_website_builder.php">
        <h3>4 · Website-Baukasten</h3>
        <p class="who">z.&nbsp;B. Wix · Squarespace · Jimdo</p>
        <p>iframe in einem HTML-Embed-Widget mit fester Höhe – der Inhalt wird auf die Widget-Höhe
           begrenzt, ein Auto-Resize ist hier nicht möglich.</p>
    </a>

    <h2>Einbindung per HTML-Tag</h2>
    <p class="group-lead">
        Ohne iframe: die Box wird als eigenes HTML-Tag direkter Teil der Seite, gegen das Seiten-CSS
        isoliert und über freigegebene Design-Variablen gestaltbar.
    </p>

    <a class="card basic" href="component.php">
        <h3>Basis: HTML-Tag</h3>
        <p>Minimal-Beispiel der Tag-Einbindung: ein Script, ein
           <code>&lt;losys-projektbox&gt;</code>-Tag.</p>
    </a>

    <a class="card" href="component_styling.php">
        <h3>Gestaltung anpassen</h3>
        <p>Die drei Gestaltungsebenen: Layout des Tags, Design-Variablen (<code>--losys-…</code>)
           und benannte Teile (<code>::part(…)</code>).</p>
    </a>

    <h2>Einbindung per div/JS (älterer Weg)</h2>
    <p class="group-lead">
        Lädt die Box per jQuery in ein <code>&lt;div&gt;</code> – für Bestandsseiten; neue
        Integrationen ohne iframe nutzen besser das HTML-Tag.
    </p>

    <a class="card basic" href="div.php">
        <h3>Basis: div/JS</h3>
        <p>Minimal-Beispiel der div/JavaScript-Einbettung (benötigt jQuery&nbsp;≥&nbsp;3).</p>
    </a>

    <a class="card" href="div_legacy_fixed.php">
        <h3>5 · Legacy + div/JS</h3>
        <p class="who">Seite mit fester Breite (960&nbsp;px) · jQuery 3.x</p>
        <p>Box per jQuery <code>.load()</code> in ein <code>&lt;div&gt;</code> – generische
           Seiten-CSS-Klassen können in die Box durchschlagen.</p>
    </a>
</div>
</body>
</html>
