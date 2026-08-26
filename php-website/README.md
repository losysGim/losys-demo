# Project-Box Website Demo

this project demonstrates how you can easily include a listing of your project-references
from referenz-verwaltung.ch into your website (without the use of the Customer API).

the "Project-Box" is a ready-made widget provided by Losys. there are three ways to
embed it into your website:

| method | base example | when to use |
|---|---|---|
| **iframe** | `iframe.php` | the recommended standard – two lines of HTML, works in practically every website |
| **HTML-tag** | `component.php` | no iframe: the box becomes part of your page, isolated from your page CSS and styleable via design variables |
| **div/JS** | `div.php` | older method for existing sites – loads the box via jQuery (≥ 3) into a `<div>` |

## Requirements
- tested with PHP 8.4 (should also work with versions 7 and 8.1)
- tested on Linux (Ubuntu 22.04.5 LTS and 24.04.2 LTS) and Windows 11
- PHP's buildin webserver is sufficient for the purpose of this demo  

## Installation
- copy the file `/config.json.example` to `/config.json`
- insert the values you received from [Losys support](mailto:support@losys.ch):
  - `link` – your personal project-box link (needed by all pages)
  - `projectbox_script` – the script URL for the HTML-tag embedding
    (only needed by the HTML-tag pages)

## Running the demo
- start PHP using `php -S '127.0.0.1:8006' -t {your_path}`  
  replace `{your_path}` with the path to the copy of this repository on 
  your local machine
- open [`http://127.0.0.1:8006/index.php`](http://127.0.0.1:8006/index.php) with a browser

## Scenarios

besides the minimal base examples the demo ships a set of example host-page setups.
each one shows the project-box embedded in a differently structured page (abstracted
to its technical core, no real content) so you can visually test the box inside the
kind of layout your own site uses:

| page | setup | shows |
|---|---|---|
| `index.php` | overview | links to all pages |
| `iframe_classic_cms.php` | classic server-rendered CMS (e.g. TYPO3/Joomla/Contao) | fixed-width content column, standard iframe + `losys.js` |
| `iframe_wordpress_builder.php` | WordPress + page-builder (e.g. Divi/Elementor) | theme CSS on `iframe`, nested builder wrappers, reveal-on-scroll (box off-screen at load) |
| `iframe_custom_grid.php` | custom build (Bootstrap 5 / CSS-Grid) | grid-track sizing (`min-width:0`), sticky header z-index, Swiper slider |
| `iframe_website_builder.php` | website builder (e.g. Wix/Squarespace/Jimdo) | fixed-height HTML-embed widget + sandbox → clipping, no auto-resize |
| `div_legacy_fixed.php` | fixed-width page + div/JS embedding | generic CSS class names colliding with the injected box markup; div/JS needs jQuery ≥ 3 |
| `component_styling.php` | HTML-tag embedding + page CSS | the three styling layers: tag layout, design variables (`--losys-…`), named parts (`::part(…)`) |
