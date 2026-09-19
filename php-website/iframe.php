<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Project-Box – Einbindung per iframe</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php losys_switcher_css(); ?>
    <style>
        body { margin: 0; font-family: system-ui, "Segoe UI", sans-serif; color: #1f2933; line-height: 1.55; }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 24px 20px 64px; }
        h1 { font-size: 24px; }
        pre { background: #f5f7fa; border: 1px solid #e4e7eb; border-radius: 6px; padding: 12px 14px;
            font-size: 13px; overflow-x: auto; }
    </style>
</head>
<body>
<?php losys_nav('iframe.php'); ?>
<div class="wrap">
    <h1>Einbindung per iframe (Basis-Beispiel)</h1>

    <p>
        Die <strong>Project-Box</strong> ist ein fertiges Widget von Losys, das die Projektreferenzen
        Ihres Unternehmens anzeigt – mit Liste, Filtern, Detailansicht und optional Karte und PDF.
        Sie erhalten von Losys eine persönliche Web-Adresse für Ihre Box (den «Box-Link»).
        Zwei Zeilen HTML genügen, um sie in Ihre Webseite einzubinden:
    </p>

    <pre>&lt;iframe
    id="losysReferences"
    title="Projektreferenzen"
    style="width: 1px; min-width: 100%; border: none;"
    src="IHR-BOX-LINK"
&gt;&lt;/iframe&gt;
&lt;script src="IHR-BOX-LINK/losys.js"&gt;&lt;/script&gt;</pre>

    <p class="demo-note">
        <strong><code>losys.js</code> nicht weglassen:</strong> das Script passt die Höhe des iframes
        automatisch an den Inhalt der Box an. Ohne dieses Script zeigt die Box nach kurzer Zeit einen
        eigenen Scrollbalken – der Inhalt bleibt erreichbar, wächst aber nicht mehr mit der Seite mit.
        Die Angaben <code>id="losysReferences"</code> und <code>style="width: 1px; min-width: 100%;"</code>
        sorgen dafür, dass das Script gezielt dieses iframe anpasst und die Box die volle Breite
        ihres Umfelds nutzt.
    </p>

    <p class="demo-note">
        <strong>Ihre Webseite bleibt unverändert:</strong> welches Design und welche Darstellungstechnik
        Ihre Box nutzt, wird bei Losys eingestellt – das Einbindungs-Snippet oben bleibt dabei immer
        gleich. Über Parameter am Box-Link lassen sich zusätzlich Filter vorbelegen
        (z.&nbsp;B. <code>?companyIds[]=…</code>) oder die Anzeigesprache festlegen
        (z.&nbsp;B. <code>?lang=fr</code>).
    </p>

    <p>Unterhalb dieses Texts ist die Box mit genau diesem Snippet eingebunden:</p>

    <!-- ===== Project-Box: embedded via iframe + losys.js resizer ===== -->
    <iframe
        id="losysReferences"
        title="Projektreferenzen"
        style="width: 1px; min-width: 100%; border: none;"
        src="<?php echo htmlspecialchars($link); ?>"
    ></iframe>
    <script src="<?php echo htmlspecialchars($link); ?>/losys.js"></script>
</div>
</body>
</html>
