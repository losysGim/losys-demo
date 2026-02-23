# Basic PHP Demo

this project demonstrates how you can use the Losys Customer API to integrate your
project-data at Losys into your company website using PHP.

## Requirements

- PHP 8.5 (should also work with version 8.1 or 8.4) with the extensions `ext-json` and `ext-intl`
- tested on Linux (Ubuntu 24.04.2 LTS) and Windows 11
- PHP's buildin webserver is sufficient for the purpose of this demo  

## Installation
- install the required libraries with `composer install`
- copy the file `/.env.example` to `/.env`
- edit the file `/.env` and insert your Losys API-credentials  
  to receive your credentials contact [support@losys.ch](mailto:support@losys.ch)

## Running the demo
- start PHP using `php -S '127.0.0.1:8005' -t {your_path}/public`  
  replace `{your_path}` with the path to the copy of this repository on 
  your local machine
- open [`http://127.0.0.1:8005`](http://127.0.0.1:8005) with a browser