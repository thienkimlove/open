<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Route;

class Permission
{
    public static function all()
    {
        $results = [];

        foreach (Route::getRoutes() as $route) {
            $results[] = self::filterRoute($route);
        }

        $results = Arr::sort($results, function ($value) {
            return $value['uri'];
        });

        return array_filter($results);
    }

    protected static function filterRoute($route)
    {
        if (! in_array('acl', array_values($route->middleware()))) {
            return;
        }

        $result = [
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action' => $route->getActionName(),
        ];

        if ($result['action'] == 'Closure' || is_null($result['name'])) {
            return;
        }

        return array_merge($result, ['controller' => explode('.', $result['name'])[0]]);
    }
}