<?php require __DIR__ . '/_inc.php'; $link = losys_link(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Szenario 3 – Custom / Bootstrap-Grid</title>
    <!-- modern stack: Bootstrap 5 + Swiper, no jQuery required -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" crossorigin="anonymous">
    <?php losys_switcher_css(); ?>
    <style>
        /*
         * scenario 3 – custom build with Bootstrap 5 + CSS-Grid + Swiper.
         * reproduces situations common in modern, component-driven sites:
         *   (a) a CSS-GRID page layout (sidebar + main). grid items default to
         *       min-width:auto, which lets a wide iframe blow out the track – the
         *       classic fix `min-width: 0` on the grid cell is applied & commented.
         *   (b) a STICKY header with a high z-index that can overlap sticky/absolute
         *       elements rendered by the box.
         *   (c) a Swiper slider directly above the box (its own width/overflow context).
         */
        body { margin: 0; }
        .topbar { position: sticky; top: 0; z-index: 1030; background: #0d6efd; color: #fff;
            padding: 12px 0; box-shadow: 0 2px 10px #00000022; }
        .layout { display: grid; grid-template-columns: 260px 1fr; gap: 28px;
            max-width: 1200px; margin: 28px auto; padding: 0 16px; }
        .layout > main { min-width: 0; } /* <-- essential: without it the iframe overflows the grid track */
        .sidebar .list-group-item.active { background: #0d6efd; border-color: #0d6efd; }
        .swiper { height: 200px; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
        .swiper-slide { display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; }
        @media (max-width: 800px) { .layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?php losys_nav('iframe_custom_grid.php'); ?>

<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="fs-5 fw-bold">elettro&nbsp;solutions</span>
        <nav><a class="text-white text-decoration-none me-3" href="#">Über uns</a>
             <a class="text-white text-decoration-none me-3" href="#">Leistungen</a>
             <a class="text-white text-decoration-none" href="#" aria-current="page">Referenzen</a></nav>
    </div>
</div>

<div class="layout">
    <aside class="sidebar">
        <div class="list-group">
            <a class="list-group-item list-group-item-action" href="#">Übersicht</a>
            <a class="list-group-item list-group-item-action active" href="#">Referenzen</a>
            <a class="list-group-item list-group-item-action" href="#">Zertifikate</a>
            <a class="list-group-item list-group-item-action" href="#">Downloads</a>
        </div>
    </aside>

    <main>
        <!-- (c) Swiper slider above the box -->
        <div class="swiper" id="heroSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide" style="background:#0d6efd;">Projekt A</div>
                <div class="swiper-slide" style="background:#198754;">Projekt B</div>
                <div class="swiper-slide" style="background:#6610f2;">Projekt C</div>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <h1 class="h3 mb-3">Unsere Referenzen</h1>
        <div class="demo-note">
            <strong>Szenario 3 – Custom / Bootstrap-Grid.</strong> Die Box sitzt in der
            <code>1fr</code>-Spalte eines CSS-Grids. Entscheidend ist <code>min-width: 0</code> auf der
            Grid-Zelle – sonst sprengt das <code>min-width:100%</code>-iframe die Spaltenbreite.
            Zusätzlich: sticky Header (<code>z-index</code>) und ein Swiper-Slider direkt darüber.
        </div>

        <!-- ===== Project-Box: iframe inside a CSS-grid main column ===== -->
        <iframe id="losysReferences"
                title="Projektreferenzen"
                style="width: 1px; min-width: 100%; border: none;"
                src="<?php echo htmlspecialchars($link); ?>"></iframe>
        <script src="<?php echo htmlspecialchars($link); ?>/losys.js"></script>
        <!-- ============================================================= -->
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" crossorigin="anonymous"></script>
<script>
    new Swiper('#heroSwiper', { loop: true, autoplay: { delay: 2500 }, pagination: { el: '.swiper-pagination' } });
</script>
</body>
</html>
