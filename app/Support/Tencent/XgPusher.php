<?php

namespace App\Support\Tencent;

use ElfSundae\XgPush\ClickAction;
use ElfSundae\XgPush\Message;
use ElfSundae\XgPush\MessageIOS;
use ElfSundae\XgPush\Style;
use ElfSundae\XgPush\XingeApp;

class XgPusher
{
    /**
     * The XingeApp instance.
     *
     * @var \ElfSundae\XgPush\XingeApp
     */
    protected $xinge;

    /**
     * The pusher environment.
     *
     * 向iOS设备推送时必填，1表示推送生产环境；2表示推送开发环境。推送Android平台不填或填0.
     *
     * @var int
     */
    protected $environment = XingeApp::IOSENV_DEV;

    /**
     * The key of custom payload.
     *
     * @var string
     */
    protected $customKey = 'custom';

    /**
     * Xinge account prefix.
     *
     * @warning 信鸽不允许使用简单的账号，例如纯数字的id。
     *
     * @var string
     */
    protected $accountPrefix = 'user';

    /**
     * Create a new instance.
     *
     * @param  string  $appKey
     * @param  string  $appSecret
     */
    public function __construct($appKey, $appSecret)
    {
        $this->xinge = new XingeApp($appKey, $appSecret);
    }

    /**
     * Get the XingeApp instance.
     *
     * @return \ElfSundae\XgPush\XingeApp
     */
    public function getXinge()
    {
        return $this->xinge;
    }

    /**
     * Get the app key.
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->xinge->accessId;
    }

    /**
     * Get the app secret.
     *
     * @return string
     */
    public function getAppSecret()
    {
        return $this->xinge->secretKey;
    }

    /**
     * Get the pusher environment.
     *
     * @return int
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set the pusher environment.
     *
     * @param  mixed  $env
     * @return $this
     */
    public function setEnvironment($env)
    {
        if (is_string($env)) {
            $env = $env == 'production' ? XingeApp::IOSENV_PROD : XingeApp::IOSENV_DEV;
        }

        if (is_numeric($env)) {
            $this->environment = $env;
        }

        return $this;
    }

    /**
     * Get the key of custom payload.
     *
     * @return string
     */
    public function getCustomKey()
    {
        return $this->customKey;
    }

    /**
     * Set the key of custom payload.
     *
     * @param  string  $key
     * @return $this
     */
    public function setCustomKey($key)
    {
        if ($key) {
            $this->customKey = $key;
        }

        return $this;
    }

    /**
     * Get the account prefix.
     *
     * @return string
     */
    public function getAccountPrefix()
    {
        return $this->accountPrefix;
    }

    /**
     * Set the account prefix.
     *
     * @param  string  $prefix
     * @return $this
     */
    public function setAccountPrefix($prefix)
    {
        $this->accountPrefix = $prefix;

        return $this;
    }

    /**
     * Determine if the Xinge response is success.
     *
     * @see http://developer.qq.com/wiki/xg/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%8E%A5%E5%85%A5/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97.html
     *
     * @param  mixed  $response
     * @return bool
     */
    public function succeed($response)
    {
        return $this->code($response) === 0;
    }

    /**
     * Get the code of Xinge response.
     *
     * @param  mixed  $response
     * @return int
     */
    public function code($response)
    {
        return is_array($response) && isset($response['ret_code']) ? $response['ret_code'] : -999999;
    }

    /**
     * Get the error message of Xinge response.
     *
     * @param  mixed  $response
     * @return string|null
     */
    public function message($response)
    {
        if (is_array($response)) {
            return array_get($response, 'err_msg');
        }
    }

    /**
     * Get the result data of Xinge response.
     *
     * @param  mixed  $response
     * @return mixed
     */
    public function result($response, $key = null)
    {
        if (is_array($response)) {
            return array_get($response, $key ? "result.{$key}" : 'result');
        }
    }

    /**
     * Encode the custom data.
     *
     * @param  mixed  $data
     * @return array|null
     */
    public function encodeCustomData($data)
    {
        if (! empty($data)) {
            return [$this->customKey => $data];
        }
    }

    /**
     * Get Xinge account for the given user.
     *
     * @param  mixed  $user
     * @return string
     */
    public function accountForUser($user)
    {
        if ($this->accountPrefix && is_string($user) && starts_with($user, $this->accountPrefix)) {
            return $user;
        }

        if (is_object($user)) {
            $user = $user->id;
        } elseif (is_array($user)) {
            $user = $user['id'];
        }

        return $this->accountPrefix.$user;
    }

    /**
     * Creates a new MessageIOS instance.
     *
     * @param  string  $alert
     * @param  mixed  $custom
     * @param  int  $badge
     * @param  string  $sound
     * @return \ElfSundae\XgPush\MessageIOS
     */
    public function createIOSMessage($alert = '', $custom = null, $badge = 1, $sound = 'default')
    {
        $message = new MessageIOS();
        $message->setAlert($alert);
        if ($customData = $this->encodeCustomData($custom)) {
            $message->setCustom($customData);
        }
        if (is_numeric($badge) && $badge >= 0) {
            $message->setBadge($badge);
        }
        if ($sound) {
            $message->setSound($sound);
        }

        return $message;
    }

    /**
     * Create a new Message instance.
     *
     * @param  string  $content
     * @param  mixed  $custom
     * @param  string  $title
     * @param  int  $type
     * @return \ElfSundae\XgPush\Message
     */
    public function createAndroidMessage($content = '', $custom = null, $title = null, $type = Message::TYPE_NOTIFICATION)
    {
        $message = new Message();
        $message->setTitle($title ?: config('app.name'));
        $message->setContent($content);
        if ($customData = $this->encodeCustomData($custom)) {
            $message->setCustom($customData);
        }
        $message->setType($type);
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
    public function queryDeviceTokensForUser($user)
    {
        $result = $this->xinge->QueryTokensOfAccount($this->accountForUser($user));

        if ($this->succeed($result)) {
            return array_get($result, 'result.tokens', []);
        }
    }

    /**
     * Query all tags for the given device token.
     *
     * @param  string  $deviceToken
     * @return string[]|null
     */
    public function queryTagsForDeviceToken($deviceToken)
    {
        $result = $this->xinge->QueryTokenTags($deviceToken);

        if ($this->succeed($result)) {
            return array_get($result, 'result.tags', []);
        }
    }

    /**
     * Query all tags for the given user.
     *
     * @param  mixed  $user
     * @param  array  &$deviceTokens
     * @return array
     */
    public function queryTagsForUser($user, &$deviceTokens = null)
    {
        $deviceTokens = $this->queryDeviceTokensForUser($user);

        $result = [];
        foreach ($deviceTokens as $token) {
            if ($tags = $this->queryTagsForDeviceToken($token)) {
                $result[$token] = $tags;
            }
        }

        return $result;
    }

    /**
     * Dynamically handle calls to the XingeApp instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->xinge, $method], $parameters);
    }
}
