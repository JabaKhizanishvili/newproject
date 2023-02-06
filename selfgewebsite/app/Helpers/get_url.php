<?php


/**
 * @param $lang
 *
 * @return string
 */
function get_url($lang): string
{

    $host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    $uriSegments[1] = $lang;


    $uriSegments = implode('/', $uriSegments);
    return $host . $uriSegments;
}
