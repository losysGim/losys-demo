<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Szenario 1 – Klassisches CMS (iframe)</title>
    <?php losys_switcher_css(); ?>
    <style>
        /*
         * scenario 1 – classic server-rendered CMS (e.g. TYPO3 / Joomla / Contao):
         * a page with a single centered content column of fixed max-width and normal
         * document flow, with the standard iframe embedding.
         */
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Georgia, "Times New Roman", serif; color: #222; background: #f4f4f2; }
        header.site { background: #34495e; color: #fff; }
        .bar { max-width: 1140px; margin: 0 auto; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; }
        .bar nav a { color: #d6dee6; text-decoration: none; margin-left: 18px; }
        .hero { background: #46627f; color: #fff; }
        .hero .inner { max-width: 1140px; margin: 0 auto; padding: 38px 20px; }
        main.content { max-width: 1140px; margin: 0 auto; padding: 28px 20px 60px; }
        main.content h1 { font-size: 28px; }
        footer.site { background: #2c3e50; color: #aebfd0; }
        footer .inner { max-width: 1140px; margin: 0 auto; padding: 22px 20px; font: 13px/1.6 system-ui, sans-serif; }
    </style>
</head>
<body>
<?php losys_nav('iframe_classic_cms.php'); ?>

<header class="site">
    <div class="bar">
        <div style="font-size:20px;font-weight:bold;">Muster Bau AG</div>
        <nav><a href="#">Firma</a><a href="#">Leistungen</a><a href="#" aria-current="page">Referenzen</a><a href="#">Kontakt</a></nav>
    </div>
</header>

<section class="hero"><div class="inner"><h2 style="margin:0;">Unsere Referenzen</h2>
    <p style="margin:.4em 0 0;opacity:.85;">Eine Auswahl realisierter Projekte.</p></div></section>

<main class="content">
    <h1>Realisierte Projekte</h1>
    <p>Serverseitig gerenderter Seitenaufbau mit einer zentrierten Content-Spalte fester
       Maximalbreite (<code>max-width: 1140px</code>) im normalen Dokumentenfluss.</p>

    <div class="demo-note">
        <strong>Szenario 1 – klassisches CMS.</strong> Standard-iframe-Einbettung mit dem
        <code>width:1px; min-width:100%</code>-Idiom + <code>losys.js</code>-Resizer.
        Die Box nimmt die volle Breite ein und wächst automatisch in der Höhe.
    </div>

    <!-- ===== Project-Box: standard iframe embedding ===== -->
    <iframe id="losysReferences"
            title="Projektreferenzen"
            style="width: 1px; min-width: 100%; border: none;"
            src="<?php echo htmlspecialchars($link); ?>"></iframe>
    <script src="<?php echo htmlspecialchars($link); ?>/losys.js"></script>
    <!-- ======================================================================= -->
</main>

<footer class="site"><div class="inner">© Muster Bau AG – Demo-Seite für die Losys Project-Box (klassisches CMS).</div></footer>
</body>
</html>
