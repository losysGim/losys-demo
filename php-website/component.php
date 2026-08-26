<?php require __DIR__ . '/_inc.php'; $link = losys_link(); $script = losys_projectbox_script(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Project-Box – Einbindung per HTML-Tag</title>
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
<?php losys_nav('component.php'); ?>
<div class="wrap">
    <h1>Einbindung per HTML-Tag (Basis-Beispiel)</h1>

    <p>
        Neben dem <a href="iframe.php">iframe</a> lässt sich die Project-Box auch als eigenes
        <strong>HTML-Tag</strong> einbinden – ganz ohne iframe. Die Box wird damit direkter Teil
        Ihrer Seite; ein Höhenabgleich-Script wie beim iframe ist nicht nötig. Sie benötigen dafür
        zwei Angaben von Losys: Ihren Box-Link und die Script-Adresse.
    </p>

    <pre>&lt;script type="module" src="SCRIPT-ADRESSE"&gt;&lt;/script&gt;
&lt;losys-projektbox link="IHR-BOX-LINK"&gt;
    &lt;p&gt;Die Projektübersicht kann zurzeit nicht angezeigt werden.&lt;/p&gt;
&lt;/losys-projektbox&gt;</pre>

    <p class="demo-note">
        <strong>Sauber getrennt von Ihrer Seite:</strong> der Inhalt der Box wird in einem
        <em>Shadow&nbsp;DOM</em> gerendert. Ihr Seiten-CSS kann das Innere der Box nicht versehentlich
        verändern – und die Box verändert nichts an Ihrer Seite. Wie Sie die Box trotzdem gezielt
        an Ihr Erscheinungsbild anpassen, zeigt die Seite
        <a href="component_styling.php">HTML-Tag · Gestaltung</a>.
    </p>

    <p class="demo-note">
        <strong>Ersatzinhalt für den Notfall:</strong> Zwischen dem öffnenden und dem schliessenden
        <code>&lt;losys-projektbox&gt;</code>-Tag können Sie beliebiges HTML hinterlegen. Solange die
        Box lädt, bleibt dieser Inhalt unsichtbar – die Box rendert in ihrem eigenen
        Shadow&nbsp;DOM und zeigt ihn nicht an. Lässt sich das Script dagegen gar nicht laden – etwa
        weil die Content-Security-Policy Ihrer Seite unsere Adressen nicht erlaubt –, bleibt der
        Ersatzinhalt stehen: Ihre Besucher sehen eine Meldung statt einer leeren Stelle.
        Auf langsamen Verbindungen kann der Ersatzinhalt kurz aufblitzen, bevor die Box übernimmt –
        halten Sie ihn deshalb kurz.
    </p>

    <p class="demo-note">
        <strong>Gut zu wissen:</strong>
        Mehrere Boxen auf einer Seite sind möglich – je ein <code>&lt;losys-projektbox&gt;</code>-Tag,
        das Script nur einmal. Mit den Attributen <code>lang="fr"</code> und
        <code>tracking="off"</code> lassen sich Anzeigesprache und Nutzungsauswertung je Einbindung
        steuern. Welche Einbindungsart für Ihre Box eingerichtet ist, steuert Losys – für die
        Tag-Einbindung wenden Sie sich an
        <a href="mailto:support@losys.ch">support@losys.ch</a>.
    </p>

    <p>Unterhalb dieses Texts ist die Box mit genau diesem Snippet eingebunden:</p>

    <!-- ===== Project-Box: embedded as HTML-tag (web component, no iframe) ===== -->
    <!-- the content between the tags is the fallback: invisible while the box
         renders (shadow DOM), visible if the runtime cannot be loaded at all. -->
    <script type="module" src="<?php echo htmlspecialchars($script); ?>"></script>
    <losys-projektbox link="<?php echo htmlspecialchars($link); ?>">
        <p>Die Projektübersicht kann zurzeit nicht angezeigt werden.</p>
    </losys-projektbox>
</div>
</body>
</html>
