# Project-Box Website Demo

this project demonstrates how you can easily include a listing of your project-references
from referenz-verwaltung.ch into your website (without the use of the Customer API).

## Requirements
- tested with PHP 8.1 (should also work with version 7)
- tested on Linux (Ubuntu 22) and Windows 11
- PHP's buildin webserver is sufficient for the purpose of this demo  

## Installation
- edit the file `/config.json` and insert the customized link to your Project-Box.  
  to receive your link contact [support@losys.ch](mailto:support@losys.ch)

## Running the demo
- start PHP using `php -S '127.0.0.1:8006' -t {your_path}`  
  replace `{your_path}` with the path to the copy of this repository on 
  your local machine
- open [`http://127.0.0.1:8006/iframe.php`](http://127.0.0.1:8006/iframe.php) with a browser