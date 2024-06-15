<?php

namespace Manta\Middleware;

use Manta\Models\Routeseo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class GetRouteSeo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = Route::currentRouteName();
        // dd($routeName);
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

        view()->share('routeseo', translate($route)['result']);

        return $next($request);
    }
}
