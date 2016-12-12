<?php

namespace App\Support\Tencent;

use ElfSundae\XgPush\ClickAction;
use ElfSundae\XgPush\Message;
use ElfSundae\XgPush\MessageIOS;
use ElfSundae\XgPush\Style;
use ElfSundae\XgPush\XingeApp;
use Illuminate\Support\Str;

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
     * The key for custom payload.
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
     * @param  mixed  $environment
     * @param  string  $customKey
     */
    public function __construct($appKey, $appSecret, $environment, $customKey)
    {
        $this->xinge = new XingeApp($appKey, $appSecret);
        $this->setEnvironment($environment);
        $this->setCustomKey($customKey);
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
     * Get the key for custom payload.
     *
     * @return string
     */
    public function getCustomKey()
    {
        return $this->customKey;
    }

    /**
     * Set the key for custom payload.
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
     * Get account prefix.
     *
     * @return string
     */
    public function getAccountPrefix()
    {
        return $this->accountPrefix;
    }

    /**
     * Set account prefix.
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
     * Determine if the Xinge result is success.
     *
     * @see http://developer.qq.com/wiki/xg/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%8E%A5%E5%85%A5/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97.html
     *
     * @param  mixed  $result
     * @return bool
     */
    public function succeed($result)
    {
        return is_array($result) && isset($result['ret_code']) && $result['ret_code'] === 0;
    }

    /**
     * Encode the custom data.
     *
     * @param  mixed  $data
     * @return array|null
     */
    public function encodeCustomData($data)
    {
        if (!empty($data)) {
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
        if ($this->accountPrefix && is_string($user) && Str::startsWith($user, $this->accountPrefix)) {
            return $user;
        }

        return $this->accountPrefix.get_id($user);
    }

    /**
     * Creates a MessageIOS instance.
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
        if (! empty($sound)) {
            $message->setSound($sound);
        }

        return $message;
    }

    /**
     * Create a Message instance.
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
