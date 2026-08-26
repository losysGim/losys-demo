<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Szenario 2 – WordPress + Page-Builder</title>
    <!-- WordPress always ships jQuery; page-builders (Divi/Elementor) rely on it. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <?php losys_switcher_css(); ?>
    <style>
        /*
         * scenario 2 – WordPress + page-builder (e.g. Divi / Elementor / WPBakery).
         * reproduces three situations that are common in such layouts:
         *
         *   (a) aggressive theme resets on replaced elements:
         *         img, iframe, embed, object { max-width: 100%; height: auto; }
         *       the `height: auto` in particular fights an auto-height iframe resizer.
         *   (b) deeply nested builder wrappers (section > row > column > module) with
         *       their own paddings and a full-width section that re-centers an inner row.
         *   (c) reveal-on-scroll animation: the module wrapping the box starts
         *       `opacity:0; transform: translateY(...)` and is revealed via
         *       IntersectionObserver. if the box is BELOW THE FOLD at load, a resizer
         *       can measure height 0 on a display-collapsed / not-yet-painted element.
         *       a tall spacer above pushes the box off-screen on purpose.
         */
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, "Helvetica Neue", Arial, sans-serif; color: #2d2d2d; }

        /* (a) theme reset on replaced elements – intentionally includes iframe */
        img, iframe, embed, object, video { max-width: 100%; height: auto; }

        .et_header { position: sticky; top: 0; z-index: 1000; background: #fff; box-shadow: 0 1px 6px #00000018; }
        .et_header .row { max-width: 1080px; margin: 0 auto; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; }
        .et_header a { color: #6b3fa0; text-decoration: none; margin-left: 16px; font-weight: 600; }

        /* (b) builder section/row/column/module nesting */
        .et_pb_section { padding: 54px 0; }
        .et_pb_section--hero { background: linear-gradient(135deg,#6b3fa0,#a96bd8); color: #fff; }
        .et_pb_row { max-width: 1080px; margin: 0 auto; padding: 0 20px; }
        .et_pb_column { padding: 0 12px; }
        .et_pb_module { margin-bottom: 24px; }
        h1 { font-size: 30px; }

        /* (c) reveal-on-scroll */
        .reveal { opacity: 0; transform: translateY(48px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.is-visible { opacity: 1; transform: none; }
        .spacer { height: 1100px; display: flex; align-items: center; justify-content: center;
            color: #b9a0d6; font-size: 14px; background: repeating-linear-gradient(45deg,#faf7ff,#faf7ff 18px,#f3ecfb 18px,#f3ecfb 36px); }
        footer { background: #2a1b3d; color: #cbb8e6; padding: 22px 0; }
    </style>
</head>
<body>
<?php losys_nav('iframe_wordpress_builder.php'); ?>

<header class="et_header">
    <div class="row">
        <div style="font-size:20px;font-weight:800;color:#6b3fa0;">FensterWerk&nbsp;GmbH</div>
        <nav><a href="#">Home</a><a href="#">Produkte</a><a href="#" aria-current="page">Referenzen</a><a href="#">Kontakt</a></nav>
    </div>
</header>

<section class="et_pb_section et_pb_section--hero">
    <div class="et_pb_row"><div class="et_pb_column">
        <h1 style="margin:0;">Referenzprojekte</h1>
        <p style="opacity:.9;">Mit dem Divi-/Elementor-typischen Aufbau gerendert.</p>
    </div></div>
</section>

<section class="et_pb_section">
    <div class="et_pb_row"><div class="et_pb_column">
        <div class="demo-note">
            <strong>Szenario 2 – WordPress + Page-Builder.</strong> Drei Punkte, die die Einbettung
            beeinflussen können: (a) der Theme-CSS <code>iframe { max-width:100%; height:auto }</code> oben,
            (b) die verschachtelten <code>.et_pb_*</code>-Wrapper,
            (c) die <code>reveal</code>-Animation – die Box steht <em>unter</em> dem 1100&nbsp;px-Platzhalter und
            wird erst beim Scrollen eingeblendet. So lässt sich prüfen, ob der Resizer die Höhe korrekt
            ermittelt, obwohl die Box beim Laden ausserhalb des sichtbaren Bereichs liegt.
        </div>

        <!-- tall spacer pushes the box below the fold on purpose (see (c) above) -->
        <div class="spacer">↓ scrollen – die Box folgt weiter unten (bewusst off-screen beim Laden) ↓</div>

        <!-- the box lives inside a reveal-on-scroll module -->
        <div class="et_pb_module reveal" id="refModule">
            <h2>Unsere Projekte</h2>
            <!-- ===== Project-Box: iframe inside builder module + reveal wrapper ===== -->
            <iframe id="losysReferences"
                    title="Projektreferenzen"
                    style="width: 1px; min-width: 100%; border: none;"
                    src="<?php echo htmlspecialchars($link); ?>"></iframe>
            <script src="<?php echo htmlspecialchars($link); ?>/losys.js"></script>
            <!-- ====================================================================== -->
        </div>
    </div></div>
</section>

<footer><div class="et_pb_row">© FensterWerk GmbH – Demo (WordPress + Page-Builder).</div></footer>

<script>
    // reveal-on-scroll, as shipped by typical builder animation modules
    document.addEventListener('DOMContentLoaded', function () {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
        }, { threshold: 0.05 });
        document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    });
</script>
</body>
</html>
