<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <!-- legacy-style page: no viewport meta, fixed 960px width.
         IMPORTANT: the div/JS embedding requires a MODERN jQuery (>= 3.x). with jQuery 1.x it
         breaks ("dbg is not defined") because the box's inline `const`/`let` bootstrap variables
         do not survive jQuery 1.x's indirect-eval script execution (`.load()` -> jQuery.globalEval
         -> window.eval). jQuery 3.x runs the fragment scripts as real <script> elements, so the
         globals stay visible to project_box.js. -> use jQuery 3.x for div/JS embedding. -->
    <title>Szenario 5 – Legacy + div/JS</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <?php losys_switcher_css(); ?>
    <style>
        /*
         * scenario 5 – fixed-width page + div/JS embedding.
         * a centered FIXED 960px container with GENERIC global class names.
         * (note: uses jQuery 3.x – the div/JS embedding needs jQuery >= 3, see <head>.)
         *
         * here the box is injected via jQuery .load() into a <div> (not an iframe),
         * so the box markup becomes part of THIS document and inherits its CSS.
         * the generic rules below (.row, .title, table, img, input) therefore bleed
         * into the project-box (css class collision).
         * mitigation (shown in the basic div.php demo): prefix your own classes.
         */
        body { margin: 0; background: #e8e8e8; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #333; }
        .page { width: 960px; margin: 0 auto; background: #fff; border: 1px solid #ccc; }
        .header { background: #7a0000; color: #fff; padding: 14px 18px; }
        .header h1 { margin: 0; font-size: 22px; }
        .menu { background: #4a4a4a; }
        .menu a { display: inline-block; color: #eee; text-decoration: none; padding: 8px 14px; }
        .body { padding: 18px; }

        /* ---- GENERIC names that collide with the project-box markup ---- */
        .row { background: #fff4cc; border-bottom: 1px dashed #d9b400; padding: 4px 0; } /* box also uses .row */
        .title { color: #7a0000; font-weight: bold; text-transform: uppercase; }          /* box also uses .title */
        table { border-collapse: collapse; width: 100%; }
        table td { border: 1px solid #ddd; }
        img { border: 3px solid #7a0000; }                                                /* every box image gets a red border */
        input[type="text"] { background: #ffe0e0; }                                       /* box search field gets tinted */
        /* ---------------------------------------------------------------- */

        .footer { background: #4a4a4a; color: #bbb; padding: 10px 18px; }
    </style>
</head>
<body>
<?php losys_nav('div_legacy_fixed.php'); ?>

<div class="page">
    <div class="header"><h1>Schreinerei Muster</h1></div>
    <div class="menu"><a href="#">Start</a><a href="#">Produkte</a><a href="#" style="background:#7a0000;">Referenzen</a><a href="#">Kontakt</a></div>

    <div class="body">
        <div class="title">Unsere Referenzen</div>

        <div class="demo-note">
            <strong>Szenario 5 – feste Breite + div/JS.</strong> Fester 960&nbsp;px-Container und
            <em>generische</em> CSS-Klassen (<code>.row</code>, <code>.title</code>, <code>table</code>,
            <code>img</code>, <code>input</code>), die in die per <code>.load()</code> eingefügte Box
            durchschlagen können – Bilder bekommen rote Rahmen, das Suchfeld wird eingefärbt
            (<strong>CSS-Klassen-Kollision</strong>). Abhilfe: eigene Klassen prefixen (siehe Basis-<code>div.php</code>).
            <br><strong>Hinweis:</strong> die div/JS-Einbettung benötigt <strong>modernes jQuery (≥&nbsp;3)</strong>;
            mit jQuery&nbsp;1.x funktioniert sie nicht.
        </div>

        <p>Auswahl ausgeführter Arbeiten:</p>

        <!-- ===== Project-Box: injected via jQuery .load() into this div ===== -->
        <div id="losys"></div>
        <!-- ================================================================= -->
    </div>

    <div class="footer">© Schreinerei Muster – Demo (feste Breite + div/JS).</div>
</div>

<script>
    // legacy embedding: load the box markup into #losys (box brings its own JS; we skip its jQuery)
    $(document).ready(function () {
        $('#losys').load("<?php echo htmlspecialchars($link); ?>/box?skip_includes[]=jquery");
    });
</script>
</body>
</html>
