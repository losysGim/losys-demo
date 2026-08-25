<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Szenario 4 – Website-Baukasten</title>
    <?php losys_switcher_css(); ?>
    <style>
        /*
         * scenario 4 – website builder (e.g. Wix / Squarespace / Jimdo).
         * these platforms wrap custom HTML in an "HTML embed / iframe widget" that the
         * user sizes by dragging – i.e. it gets a FIXED pixel height and `overflow: hidden`.
         * the platform also commonly sandboxes the widget and blocks external <script>
         * tags, so the losys.js auto-resizer may not run. result: the box is clipped at
         * the widget height and an inner scrollbar appears.
         *
         * this page reproduces that constraint so the clipping is visible.
         */
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Helvetica Neue", Arial, sans-serif; color: #23262d; background: #fff; }
        .wix-header { text-align: center; padding: 26px 16px; border-bottom: 1px solid #eee; }
        .wix-header .brand { font-size: 24px; letter-spacing: 3px; font-weight: 300; }
        .wix-section { max-width: 980px; margin: 0 auto; padding: 32px 16px 64px; }

        /* the draggable "HTML embed" widget: fixed height + clipped, as on Wix/Squarespace */
        .html-embed-widget {
            position: relative;
            width: 100%;
            height: 600px;              /* <-- fixed, user-dragged height */
            overflow: hidden;           /* <-- platform clips overflow */
            border: 1px dashed #c9ced6; /* visualises the widget bounds for the demo */
            background: #fafbfc;
        }
        .html-embed-widget > iframe { width: 100%; height: 100%; border: 0; }
        .widget-label { position: absolute; top: 6px; right: 8px; z-index: 2; font: 11px/1 monospace;
            color: #9aa3af; background: #fff; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
<?php losys_nav('iframe_website_builder.php'); ?>

<div class="wix-header"><div class="brand">B A U &nbsp; S T U D I O</div></div>

<div class="wix-section">
    <h1 style="font-weight:300;">Referenzen</h1>

    <div class="demo-note">
        <strong>Szenario 4 – Website-Baukasten (Wix/Squarespace/Jimdo).</strong>
        Der Baukasten steckt eingebettetes HTML in ein „HTML-Embed"-Widget mit per Maus gezogener
        <strong>fester Höhe</strong> (<code>height: 600px; overflow: hidden</code>) und blockiert oft externe
        <code>&lt;script&gt;</code> (Sandbox) – der <code>losys.js</code>-Resizer läuft dann nicht.
        Folge: die Box wird auf Widget-Höhe <strong>abgeschnitten</strong> bzw. zeigt eine eigene Scrollbar.
    </div>

    <!-- ===== Project-Box: iframe inside a fixed-height builder widget ===== -->
    <div class="html-embed-widget">
        <span class="widget-label">HTML-Embed · height:600px · overflow:hidden</span>
        <iframe id="losysReferences" title="Projektreferenzen"
                src="<?php echo htmlspecialchars($link); ?>"></iframe>
    </div>
    <!--
        NOTE: losys.js is intentionally NOT loaded here to mirror the common
        builder-sandbox situation. with builders that DO allow scripts, loading
        <?php echo htmlspecialchars($link); ?>/losys.js would still be constrained by the fixed widget height.
    -->
    <p style="color:#8a93a0;font-size:13px;">↑ Box ist auf 600&nbsp;px Widget-Höhe begrenzt – Inhalt darunter wird abgeschnitten.</p>
</div>
</body>
</html>
