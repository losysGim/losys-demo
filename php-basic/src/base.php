<?php
preg_match('%(/[^?]+)($|\\?)%', $_SERVER['REQUEST_URI'], $match);
if (substr_count($_SERVER['PHP_SELF'], '/') != 1
    || !in_array($match[1], ['/', $_SERVER['PHP_SELF']]))
{
    /*
     * PHP's buildin webserver directs all requests that do not match existing files here
     * avoid sending requests for /favicon.ico and other debugging-URLs that the browsers request in the background.
     */
    http_response_code(404);
    print "\"{$_SERVER['REQUEST_URI']}\" does not exist here";
    die();
}