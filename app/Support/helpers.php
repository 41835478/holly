<?php

use App\Support\Http\ApiResponse;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

if (! function_exists('mb_trim')) {
    /**
     * Strip whitespace (or other characters) from the beginning and end of a string.
     *
     * @see https://github.com/vanderlee/PHP-multibyte-functions/blob/master/functions/mb_trim.php
     *
     * @param  string  $string
     * @return string
     */
    function mb_trim($string)
    {
        return mb_ereg_replace('^\s*([\s\S]*?)\s*$', '\1', $string);
    }
}

if (! function_exists('urlsafe_base64_encode')) {
    /**
     * Encodes the given data with base64, and returns an URL-safe string.
     *
     * @param  string  $data
     * @return string
     */
    function urlsafe_base64_encode($data)
    {
        return strtr(base64_encode($data), ['+' => '-', '/' => '_', '=' => '']);
    }
}

if (! function_exists('urlsafe_base64_decode')) {
    /**
     * Decodes a base64 encoded data.
     *
     * @param  string  $data
     * @param  bool  $strict
     * @return string
     */
    function urlsafe_base64_decode($data, $strict = false)
    {
        return base64_decode(strtr($data.str_repeat('=', (4 - strlen($data) % 4)), '-_', '+/'), $strict);
    }
}

if (! function_exists('string_value')) {
    /**
     * Converts any type to a string.
     *
     * @param  mixed  $value
     * @return string
     */
    function string_value($value, $jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    {
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            if (method_exists($value, 'toArray')) {
                $value = $value->toArray();
            }
        }

        return is_string($value) ? $value : json_encode($value, $jsonOptions);
    }
}

if (! function_exists('active_if')) {
    /**
     * Returns string 'active' if the current request URI matches the given patterns.
     *
     * @return string
     */
    function active_if()
    {
        return call_user_func_array([app('request'), 'is'], func_get_args()) ? 'active' : '';
    }
}

if (! function_exists('get_id')) {
    /**
     * Get id from a mixed variable.
     *
     * @param  mixed  $var
     * @param  string  $key
     * @return mixed
     */
    function get_id($var, $key = 'id')
    {
        if (is_object($var)) {
            return $var->{$key};
        } elseif (is_array($var)) {
            return $var[$key];
        }

        return $var;
    }
}

if (! function_exists('str_limit2')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function str_limit2($value, $limit = 100, $end = '')
    {
        return Str::limit($value, $limit, $end);
    }
}

if (! function_exists('is_domain')) {
    /**
     * Determines the current domain equals to the given domain identifier.
     *
     * @param  string  $identifier
     * @return bool
     */
    function is_domain($identifier)
    {
        return app('request')->getHost() === config('app.domains.'.$identifier);
    }
}

if (! function_exists('app_url')) {
    /**
     * Generate an URL for the application.
     *
     * @param  string  $path
     * @param  mixed  $parameters
     * @param  string  $identifier
     * @return string
     */
    function app_url($path = '', $parameters = null, $identifier = 'site')
    {
        $path = trim($path, '/');
        if (! empty($path) && ! starts_with($path, ['?', '&', '#'])) {
            $path = '/'.$path;
        }

        if (! is_null($parameters)) {
            $query = http_build_query($parameters);
            if (! empty($query)) {
                $path .= (str_contains($path, ['?', '&', '#']) ? '&' : '?').$query;
            }
        }

        if ($identifier && ($root = config('support.url.'.$identifier))) {
            return $root.$path;
        }

        return url($path);
    }
}

if (! function_exists('revision')) {
    /**
     * Get the revisioned asset path.
     *
     * @param  string  $path
     * @return string
     */
    function revision($path)
    {
        if ($rev = array_get(config('assets'), trim($path, '/'))) {
            return $path.'?'.$rev;
        }

        return $path;
    }
}

if (! function_exists('asset_from')) {
    /**
     * Generate the URL to an asset from a custom root domain such as CDN, etc.
     *
     * @param  string  $root
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function asset_from($root, $path, $secure = null)
    {
        return app('url')->assetFrom($root, $path, $secure);
    }
}

if (! function_exists('asset_url')) {
    /**
     * Generate an asset URL.
     *
     * @param  string $path
     * @return string
     */
    function asset_url($path, $identifier = 'asset')
    {
        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        return config('support.url.'.$identifier).'/'.revision(trim($path, '/'));
    }
}

if (! function_exists('cdn_url')) {
    /**
     * Generate an asset CDN URL.
     *
     * @param  string  $path
     * @return string
     */
    function cdn_url($path)
    {
        return asset_url($path, 'cdn');
    }
}

if (! function_exists('api')) {
    /**
     * Create a new API response.
     *
     * @return \App\Support\Http\ApiResponse
     */
    function api(...$args)
    {
        return new ApiResponse(...$args);
    }
}

if (! function_exists('optimus_encode')) {
    /**
     * Encode a number with Optimus.
     *
     * @param  int  $number
     * @return int
     */
    function optimus_encode($number)
    {
        return app('optimus')->encode($number);
    }
}

if (! function_exists('optimus_decode')) {
    /**
     * Decode a number with Optimus.
     *
     * @param  int  $number
     * @return int
     */
    function optimus_decode($number)
    {
        return app('optimus')->decode($number);
    }
}

if (! function_exists('random_uuid')) {
    /**
     * Generate a version 4 (random) UUID.
     *
     * @param  bool  $hex
     * @return string
     */
    function random_uuid($hex = false)
    {
        $uuid = Uuid::uuid4();

        return $hex ? $uuid->getHex() : $uuid->toString();
    }
}
