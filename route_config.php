<?php

define(
    'AUTH_ROUTE',
    [
        "/admin"
    ]
);

define(
    'AUTH_ROUTE_IGNORE',
    [
        "/common/get_app_setting",
        "/common/set_app_setting"
    ]
);

function is_auth_route(string $route)
{
    $result = false;
    foreach (AUTH_ROUTE as $r) {
        if (strpos($route, $r) !== false) {
            if (strpos($route, $r) == 0) {
                $result = true;
            }
            foreach (AUTH_ROUTE_IGNORE as $r_ignore) {
                if ($route == $r_ignore) {
                    $result = false;
                }
            }
        }
    }
    return $result;
}
