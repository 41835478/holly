<?php

namespace App\Support\Http\Middleware;

use Closure;
use App\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Access\AuthorizationException;

class VerifyApiToken
{
    /**
     * The URIs that should be excluded from API Token verification.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->shouldPassThrough($request) ||
            $this->isValidToken($request)
        ) {
            return $next($request);
        }

        throw new AuthorizationException('API Token Mismatch');
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading(Request $request)
    {
        return in_array($request->method(), ['HEAD', 'OPTIONS']);
    }

    /**
     * Determine if the request has a URI that should be passed through verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough(Request $request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the API token is valid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isValidToken(Request $request)
    {
        if ($token = substr($request->header('X-API-TOKEN'), 4)) {
            $timestamp = (int) substr(Helper::sampleDecrypt($token, Config::get('support.api.token.key')), 4);

            return abs($timestamp - time()) <= (int) Config::get('support.api.token.valid_interval');
        }

        return false;
    }
}
