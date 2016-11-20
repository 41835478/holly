<?php

namespace App\Models;

use App\Exceptions\InvalidInputException;
use Illuminate\Database\Eloquent\Model;

class SocialAuth extends Model
{
    const SOCIAL_TYPE_WEIBO = 1;
    const SOCIAL_TYPE_WEIXIN = 2;
    const SOCIAL_TYPE_QQ = 3;

    const SOCIAL_WEIBO = 'weibo';
    const SOCIAL_WEIXIN = 'weixin';
    const SOCIAL_QQ = 'qq';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['social'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'social', 'access_token', 'refresh_token', 'uid', 'expires_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expires_at'];

    /**
     * Get the `social` attribute.
     *
     * @return string
     */
    public function getSocialAttribute()
    {
        return static::socialFromSocialType($this->social_type);
    }

    /**
     * Scope a query with social type or name.
     *
     * @param  string|int  $social
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSocial($query, $social)
    {
        return $query->where('social_type', static::toSocialType($social));
    }

    /**
     * Get all socials.
     *
     * @return array
     */
    public static function allSocials()
    {
        return [
            static::SOCIAL_WEIBO,
            static::SOCIAL_WEIXIN,
            static::SOCIAL_QQ,
        ];
    }

    /**
     * Convert social type to social name.
     *
     * @param  int  $type
     * @return string|false
     */
    public static function socialFromSocialType($type)
    {
        switch ($type) {
            case static::SOCIAL_TYPE_WEIBO:
                return static::SOCIAL_WEIBO;
            case static::SOCIAL_TYPE_WEIXIN:
                return static::SOCIAL_WEIXIN;
            case static::SOCIAL_TYPE_QQ:
                return static::SOCIAL_QQ;
            default:
                return false;
        }
    }

    /**
     * Convert social name to social type.
     *
     * @param  string  $social
     * @return int|false
     */
    public static function socialTypeFromSocial($social)
    {
        switch ($social) {
            case static::SOCIAL_WEIBO:
                return static::SOCIAL_TYPE_WEIBO;
            case static::SOCIAL_WEIXIN:
                return static::SOCIAL_TYPE_WEIXIN;
            case static::SOCIAL_QQ:
                return static::SOCIAL_TYPE_QQ;
            default:
                return false;
        }
    }

    /**
     * Convert string or int to social type.
     *
     * @param  mixed  $value
     * @return int|false
     */
    public static function toSocialType($value)
    {
        return is_string($value) ? static::socialTypeFromSocial($value) : (int) $value;
    }

    /**
     * Find records via userId and social.
     *
     * @param  mixed  $userId
     * @param  string|int  $social
     * @return \Illuminate\Database\Eloquent\Collection|static
     */
    public static function findByUser($userId, $social = null)
    {
        if (is_object($userId)) {
            $userId = $userId->id;
        } elseif (is_array($userId)) {
            $userId = $userId['id'];
        }

        $query = static::where('user_id', $userId);

        if (! is_null($social)) {
            return $query->social($social)->first();
        }

        return $query->get()->keyBy('social');
    }

    /**
     * Find record via credentials and social user.
     *
     * @param  string|int  $social
     * @param  array  $credentials
     * @return static|null
     *
     * @throws \App\Exceptions\InvalidInputException
     */
    public static function findByCredentials($social, $credentials)
    {
        if (is_array($credentials) &&
            false !== ($social_type = static::toSocialType($social))
        ) {
            extract($credentials);

            $query = static::social($social);

            if (static::SOCIAL_TYPE_WEIBO === $social_type) {
                if (! empty($access_token) && ! empty($uid)) {
                    return $query->where(function ($query) use ($access_token, $uid) {
                        $query->where('access_token', $access_token)
                            ->orWhere('uid', $uid);
                    })->first();
                }
            } elseif (static::SOCIAL_TYPE_WEIXIN === $social_type) {
                if (! empty($access_token) && ! empty($openid) && ! empty($unionid)) {
                    return $query->where(function ($query) use ($access_token, $openid, $unionid) {
                        $query->where('access_token', $access_token)
                           ->orWhere('uid', $openid)
                           ->orWhere('vendor', $unionid);
                    })->first();
                }
            } elseif (static::SOCIAL_TYPE_QQ === $social_type) {
                if (! empty($access_token) && ! empty($openid)) {
                    return $query->where(function ($query) use ($access_token, $openid) {
                        $query->where('access_token', $access_token)
                            ->orWhere('uid', $openid);
                    })->first();
                }
            }
        }

        throw new InvalidInputException('授权数据错误！');
    }

    /**
     * Create a SocialAuth record.
     *
     * @param  string|int  $social
     * @param  int $userId
     * @param  array $credentials
     * @return static
     */
    public static function createByCredentials($social, $userId, $credentials, $save = false)
    {
        $auth = new static;
        $auth->social_type = static::toSocialType($social);
        $auth->user_id = $userId;

        $auth->updateByCredentials($credentials, false);

        if ($save) {
            $auth->save();
        }

        return $auth;
    }

    /**
     * Update credentials info.
     *
     * @param  array  $credentials
     * @param  bool  $save
     * @return $this
     */
    public function updateByCredentials($credentials, $save = false)
    {
        $this->access_token = $credentials['access_token'];
        $this->refresh_token = array_get($credentials, 'refresh_token');

        if ($this->social_type == static::SOCIAL_TYPE_WEIBO) {
            $this->uid = str_limit2($credentials['uid'], 40);
        } elseif ($this->social_type == static::SOCIAL_TYPE_WEIXIN) {
            $this->uid = str_limit2($credentials['openid'], 40);
            $this->vendor = str_limit2($credentials['unionid'], 200);
        } elseif ($this->social_type == static::SOCIAL_TYPE_QQ) {
            $this->uid = str_limit2($credentials['openid'], 40);
        }

        if ($expires_in = array_get($credentials, 'expires_in')) {
            $this->expires_at = $this->freshTimestamp()->addSeconds($expires_in);
        } else {
            $this->expires_at = null;
        }

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Get user avatar URL from social user array.
     *
     * @param  string|int  $social
     * @param  array  $user
     * @return string|null
     */
    public static function getAvatarFromSocialUser($social, $user)
    {
        $type = static::toSocialType($social);

        if ($type == static::SOCIAL_TYPE_WEIBO) {
            return array_get($user, 'avatar_hd');
        } elseif ($type == static::SOCIAL_TYPE_WEIXIN) {
            return array_get($user, 'headimgurl');
        } elseif ($type == static::SOCIAL_TYPE_QQ) {
            return array_get($user, 'figureurl_qq_2');
        }
    }

    /**
     * Get user nickname from social user array.
     *
     * @param  string|int  $social
     * @param  array $user
     * @return string|null
     */
    public static function getUsernameFromSocialUser($social, $user)
    {
        $type = static::toSocialType($social);

        if ($type == static::SOCIAL_TYPE_WEIBO) {
            $username = array_get($user, 'name');
        } elseif ($type == static::SOCIAL_TYPE_WEIXIN) {
            $username = array_get($user, 'nickname');
        } elseif ($type == static::SOCIAL_TYPE_QQ) {
            $username = array_get($user, 'nickname');
        }

        if (isset($username)) {
            $username = mb_trim($username);

            return ! empty($username) ? $username : null;
        }
    }
}
