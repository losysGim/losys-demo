# Project-Box Website Demo

this project demonstrates how you can easily include a listing of your project-references
from referenz-verwaltung.ch into your website (without the use of the Customer API).

## Requirements
- tested with PHP 8.4 (should also work with versions 7 and 8.1)
- tested on Linux (Ubuntu 22.04.5 LTS and 24.04.2 LTS) and Windows 11
- PHP's buildin webserver is sufficient for the purpose of this demo  

## Installation
- edit the file `/config.json` and insert the customized link to your Project-Box.  
  to receive your link contact [support@losys.ch](mailto:support@losys.ch)

## Running the demo
- start PHP using `php -S '127.0.0.1:8006' -t {your_path}`  
  replace `{your_path}` with the path to the copy of this repository on 
  your local machine
- open [`http://127.0.0.1:8006/index.php`](http://127.0.0.1:8006/index.php) with a browser

## Scenarios

besides the two minimal examples (`iframe.php`, `div.php`) the demo ships a set of
example host-page setups. each one shows the project-box embedded in a differently
structured page (abstracted to its technical core, no real content) so you can visually
test the box inside the kind of layout your own site uses:

| page | setup | shows |
|---|---|---|
| `index.php` | overview | links to all scenarios |
| `classic_cms.php` | classic server-rendered CMS (e.g. TYPO3/Joomla/Contao) | fixed-width content column, standard iframe + `losys.js` |
| `wordpress_builder.php` | WordPress + page-builder (e.g. Divi/Elementor) | theme CSS on `iframe`, nested builder wrappers, reveal-on-scroll (box off-screen at load) |
| `custom_grid.php` | custom build (Bootstrap 5 / CSS-Grid) | grid-track sizing (`min-width:0`), sticky header z-index, Swiper slider |
| `website_builder.php` | website builder (e.g. Wix/Squarespace/Jimdo) | fixed-height HTML-embed widget + sandbox → clipping, no auto-resize |
| `legacy_fixed.php` | fixed-width page + div/JS embedding | generic CSS class names colliding with the injected box markup; div/JS needs jQuery ≥ 3 |