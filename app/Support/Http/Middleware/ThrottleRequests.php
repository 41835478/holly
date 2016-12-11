<?php

namespace App\Support\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottle;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests extends BaseThrottle
{
    /**
     * Create a 'too many attempts' response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {
        if (app('request')->expectsJson()) {
            return api('操作太频繁，请稍后再试。', 429);
        }

        return parent::buildResponse($key, $maxAttempts);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  int  $maxAttempts
     * @param  int  $remainingAttempts
     * @param  int|null  $retryAfter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        return $response;
    }
}
