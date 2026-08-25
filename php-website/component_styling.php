<?php require __DIR__ . '/_inc.php'; $link = losys_link(); $script = losys_projectbox_script(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Project-Box – HTML-Tag gestalten</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php losys_switcher_css(); ?>
    <style>
        body { margin: 0; font-family: system-ui, "Segoe UI", sans-serif; color: #1f2933; line-height: 1.55; }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 24px 20px 64px; }
        h1 { font-size: 24px; }
        pre { background: #f5f7fa; border: 1px solid #e4e7eb; border-radius: 6px; padding: 12px 14px;
            font-size: 13px; overflow-x: auto; }

        /* ===== 1. the tag itself belongs to YOUR page =====
           layout, width, spacing and visibility of the box are plain page CSS
           on the <losys-projektbox> element. */
        losys-projektbox {
            display: block;
            max-width: 960px;
            margin: 24px auto;
        }

        /* ===== 2. design variables (CSS custom properties) =====
           the design of your box exposes named values ("--losys-…") for colours,
           spacing and fonts. setting them on the tag overrides the design defaults.
           which variables exist depends on the design of your box - Losys can
           provide you the list. */
        losys-projektbox {
            --losys-color-accent: #b45309;
            --losys-color-primary: #16324f;
        }

        /* ===== 3. named parts (::part) =====
           selected elements inside the box are exposed as named "parts" so you can
           style them from the outside without reaching into the box markup.
           the part names, too, are defined by the design of your box. */
        losys-projektbox::part(card-title) {
            font-weight: 700;
        }
    </style>
</head>
<body>
<?php losys_nav('component_styling.php'); ?>
<div class="wrap">
    <h1>HTML-Tag: Gestaltung anpassen</h1>

    <p>
        Bei der Einbindung per <a href="component.php">HTML-Tag</a> ist der Inhalt der Box durch das
        Shadow&nbsp;DOM vor Ihrem Seiten-CSS geschützt. Für die Anpassung an Ihr Erscheinungsbild
        gibt es drei saubere Ebenen – alle drei sind im Quelltext dieser Seite als kommentierte
        CSS-Blöcke zu finden und hier live wirksam:
    </p>

    <pre>/* 1. Das Tag selbst gehört Ihrer Seite: Layout, Breite, Abstände */
losys-projektbox { display: block; max-width: 960px; margin: 24px auto; }

/* 2. Design-Variablen: vom Design der Box freigegebene Werte */
losys-projektbox { --losys-color-accent: #b45309; --losys-color-primary: #16324f; }

/* 3. Benannte Teile: gezielt freigegebene Elemente im Inneren */
losys-projektbox::part(card-title) { font-weight: 700; }</pre>

    <p class="demo-note">
        <strong>Rangfolge der Werte:</strong> CSS auf Ihrer Seite (wie oben) überstimmt die bei Losys
        für Ihre Box hinterlegten Werte, und diese überstimmen die Vorgaben des Designs. Sie können
        die Gestaltung also wahlweise bei Losys pflegen lassen oder direkt in Ihrer Webseite setzen.
    </p>

    <p class="demo-note">
        <strong>Namen sind Design-abhängig:</strong> welche Variablen (<code>--losys-…</code>) und
        Teil-Namen (<code>::part(…)</code>) Ihre Box anbietet, bestimmt das gewählte Design.
        Die Beispiele hier nutzen das Standard-Design; die Liste für Ihre Box erhalten Sie von
        <a href="mailto:support@losys.ch">Losys</a>.
    </p>

    <!-- ===== Project-Box: embedded as HTML-tag, styled by the CSS in <head> ===== -->
    <script type="module" src="<?php echo htmlspecialchars($script); ?>"></script>
    <losys-projektbox link="<?php echo htmlspecialchars($link); ?>"></losys-projektbox>
</div>
</body>
</html>
