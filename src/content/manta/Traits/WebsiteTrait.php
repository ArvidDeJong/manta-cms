<?php

namespace Manta\Traits;

use Manta\Models\Routeseo;
use Illuminate\Support\Facades\Route;

trait WebsiteTrait
{

    public $seo_title;
    public $seo_description;

    public function getRouteSeo()
    {
        $routeName = Route::currentRouteName();

        $route = Routeseo::where(['route' => $routeName, 'locale' => app()->getLocale()])->first();
        if ($route == null) {
            $default = Routeseo::where(['route' => $routeName, 'locale' => config('manta.locale')])->first();
            if (!$default) {
                $default = RouteSeo::Create([
                    'locale' => config('manta.locale'),
                    'route' => Route::currentRouteName(),
                    'seo_title' => env('APP_NAME'),
                ]);
            }
            $route = RouteSeo::Create([
                'pid' => $default->id ?? null,
                'locale' => app()->getLocale(),
                'route' => Route::currentRouteName(),
                'seo_title' => env('APP_NAME'),
            ]);
        }
        $this->seo_title = $route->seo_title;
        $this->seo_description = $route->seo_description;
    }
}
