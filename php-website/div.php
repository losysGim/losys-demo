<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Project-Box – Einbindung per div/JS</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php losys_switcher_css(); ?>
    <style>
        /* note the "my-" prefix on all class names of this page: with the div/JS
           embedding the box markup becomes part of THIS page, so generic class
           names could collide with the ones used inside the box. */
        body { margin: 0; font-family: system-ui, "Segoe UI", sans-serif; color: #1f2933; line-height: 1.55; }
        .my-wrap { max-width: 1000px; margin: 0 auto; padding: 24px 20px 64px; }
        h1 { font-size: 24px; }
        pre { background: #f5f7fa; border: 1px solid #e4e7eb; border-radius: 6px; padding: 12px 14px;
            font-size: 13px; overflow-x: auto; }
    </style>

    <!-- the div/JS embedding needs jQuery >= 3 (usually your website ships it already) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
<?php losys_nav('div.php'); ?>
<div class="my-wrap">
    <h1>Einbindung per div/JS (Basis-Beispiel)</h1>

    <p>
        Dieser ältere Einbindungsweg lädt die Project-Box per JavaScript in ein
        <code>&lt;div&gt;</code>-Element Ihrer Seite – für Webseiten, die kein iframe einsetzen
        wollen. Er benötigt <a target="_blank" href="https://jquery.com/">jQuery</a> ab Version&nbsp;3
        (Version&nbsp;1.x führt die mitgelieferten Scripte der Box nicht aus).
        Für neue Integrationen ohne iframe empfehlen wir stattdessen die Einbindung per
        <a href="component.php">HTML-Tag</a> – sie kommt ohne jQuery aus und ist sauber von
        Ihrem Seiten-CSS getrennt.
    </p>

    <pre>&lt;div id="losys"&gt;&lt;/div&gt;
&lt;script&gt;
    $(document).ready(() =&gt; {
        $('#losys').load("IHR-BOX-LINK/box?skip_includes[]=jquery");
    });
&lt;/script&gt;</pre>

    <p class="demo-note">
        <strong>Auf CSS-Klassennamen achten:</strong> bei diesem Weg wird der Inhalt der Box Teil
        Ihrer Seite. Generische Klassennamen Ihrer Seite können mit denen der Box kollidieren –
        beachten Sie, wie die Klassennamen dieser Seite deshalb mit <code>my-</code> vorangestellt
        sind. Der Parameter <code>skip_includes[]=jquery</code> sagt der Box, dass Ihre Seite
        jQuery bereits mitbringt.
    </p>

    <!-- ===== Project-Box: injected via jQuery .load() into this div ===== -->
    <div id="losys"></div>

    <script>
        $(document).ready(() => {
            $('#losys').load("<?php echo htmlspecialchars($link); ?>/box?skip_includes[]=jquery");
        });
    </script>
</div>
</body>
</html>
