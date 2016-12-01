<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\ApiResponse;

class AddDebugbarDataToApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof ApiResponse &&
            app()->bound('debugbar') &&
            app('debugbar')->isEnabled()
        ) {
            $response->mergeData(['_debugbar' => app('debugbar')->getData()]);
        }

        return $response;
    }
}
