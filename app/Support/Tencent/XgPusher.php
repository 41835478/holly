<?php

namespace App\Support\Tencent;

class XgPusher
{
    /**
     * The XingeApp instance.
     *
     * @var \XingeApp
     */
    protected $service;

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->service = static::createService();
    }

    /**
     * Get the XingeApp instance.
     *
     * @return \XingeApp
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Create a XingeApp instance.
     *
     * @return \XingeApp
     */
    public static function createService()
    {
        return new XingeApp(static::appKey(), static::appSecret());
    }

    /**
     * Get the app key.
     *
     * @return string
     */
    public static function appKey()
    {
        return config('services.xgpush.key');
    }

    /**
     * Get the app secret.
     *
     * @return string
     */
    public static function appSecret()
    {
        return config('services.xgpush.secret');
    }

    /**
     * Get the custom key.
     *
     * @return string
     */
    public static function customKey()
    {
        return config('services.xgpush.custom_key', 'custom');
    }

    /**
     * Get the environment.
     *
     * environment: 向iOS设备推送时必填，1表示推送生产环境；2表示推送开发环境。推送Android平台不填或填0.
     *
     * @return int
     */
    public static function environment()
    {
        return config('services.xgpush.environment') == 'production' ?
            XingeApp::IOSENV_PROD :
            XingeApp::IOSENV_DEV;
    }

    /**
     * 解析信鸽返回的结果。
     *
     * @see http://developer.qq.com/wiki/xg/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%8E%A5%E5%85%A5/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97.html
     *
     * @param  mixed    $xgResult   信鸽的请求结果
     * @param  int      &$code      返回码，0 为成功
     * @param  string   &$message   请求出错时的错误信息
     * @param  mixed    &$result    请求正确时的额外数据
     * @return bool
     */
    public static function parseResult($xgResult = null, &$code = null, &$message = null, &$result = null)
    {
        if (is_array($xgResult) && isset($xgResult['ret_code'])) {
            $code = (int) $xgResult['ret_code'];
            $message = isset($xgResult['err_msg']) ? (string) $xgResult['err_msg'] : '';
            if (isset($xgResult['result'])) {
                $result = $xgResult['result'];
            }
        } else {
            $code = -99999;
        }

        return $code === 0;
    }

    /**
     * Encode the custom data.
     *
     * @param  mixed  $data
     * @return array|null
     */
    public static function encodeCustomData($data)
    {
        return ! empty($data) ? [static::customKey() => $data] : null;
    }

    /**
     * Get Xinge account for the given user.
     *
     * @param  mixed  $user
     * @return string
     */
    public static function accountForUser($user)
    {
        if (is_object($user)) {
            $user = $user->id;
        } elseif (is_array($user)) {
            $user = $user['id'];
        }

        // 信鸽不允许使用简单的账号，例如纯数字的id。
        // 所以在 userId 前面加个 'user' 字符。
        if (starts_with($user, 'user')) {
            return $user;
        }

        return 'user'.$user;
    }

    /**
     * Creates a MessageIOS instance.
     *
     * @param  string  $alert
     * @param  mixed  $custom
     * @param  int  $badge
     * @param  string  $sound
     * @return MessageIOS
     */
    public static function createIOSMessage($alert = '', $custom = null, $badge = 1, $sound = 'default')
    {
        $message = new MessageIOS();
        $message->setAlert($alert);
        if ($customData = static::encodeCustomData($custom)) {
            $message->setCustom($customData);
        }
        if (is_numeric($badge) && $badge >= 0) {
            $message->setBadge($badge);
        }
        if (! empty($sound)) {
            $message->setSound($sound);
        }

        return $message;
    }

    /**
     * Create a Message instance.
     *
     * @param  string $content
     * @param  mixed $custom
     * @param  string $title
     * @param  int $type
     * @return Message
     */
    public static function createAndroidMessage($content = '', $custom = null, $title = null, $type = Message::TYPE_NOTIFICATION)
    {
        $message = new Message();
        $message->setTitle($title ?: config('app.name'));
        $message->setContent($content);
        if ($customData = static::encodeCustomData($custom)) {
            $message->setCustom($customData);
        }
        $message->setType($type);
        //含义:样式编号0,响铃,震动,不可从通知栏清除,不影响先前通知
        $message->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $message->setAction($action);

        return $message;
    }

    /**
     * Query all device tokens for the given user.
     *
     * @param  mixed  $user
     * @return string[]|null
     */
    public static function queryDeviceTokensForUser($user)
    {
        $query = static::createService()->QueryTokensOfAccount(static::accountForUser($user));

        if (static::parseResult($query, null, null, $result)) {
            return is_array($result) ? array_get($result, 'tokens', []) : [];
        }
    }

    /**
     * Query all tags for the given device token.
     *
     * @param  string  $deviceToken
     * @return string[]|null
     */
    public static function queryTagsForDeviceToken($deviceToken)
    {
        $query = static::createService()->QueryTokenTags($deviceToken);

        if (static::parseResult($query, null, null, $result)) {
            return is_array($result) ? array_get($result, 'tags', []) : [];
        }
    }

    /**
     * Query all tags for the given user.
     *
     * @param  mixed  $user
     * @param  array  &$deviceTokens
     * @return array|null
     */
    public static function queryTagsForUser($user, &$deviceTokens = null)
    {
        $deviceTokens = static::queryDeviceTokensForUser($user);

        $result = [];
        foreach ($deviceTokens as $token) {
            if ($tags = static::queryTagsForDeviceToken($token)) {
                $result[$token] = $tags;
            }
        }

        return $result;
    }
}
