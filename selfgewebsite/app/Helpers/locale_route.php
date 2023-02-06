<?php
/**
 *  app/Helpers/lang_route_helpers.php
 *
 * Date-Time: 04.06.21
 * Time: 14:35
 * @author Insite LLC <hello@insite.international>
 */

use Illuminate\Support\Arr;

/**
 * @param $name
 * @param array|string $parameters
 * @param bool $absolute
 *
 * @return string
 */
function locale_route($name, $parameters = [], bool $absolute = true): string
{
    $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    $parameters = array_merge([
        'locale' => $uriSegments[1]
    ], Arr::wrap($parameters));


    return route($name, $parameters, $absolute);
}