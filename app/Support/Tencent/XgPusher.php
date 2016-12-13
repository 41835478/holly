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
    protected $customKey;

    /**
     * Xinge account prefix.
     *
     * @warning 信鸽不允许使用简单的账号，例如纯数字的id。
     *
     * @var string
     */
    protected $accountPrefix;

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

        if (is_int($env)) {
            $this->environment = $env;
        }

        return $this;
    }

    /**
     * Get the key of custom payload.
     *
     * @return string|null
     */
    public function getCustomKey()
    {
        return $this->customKey;
    }

    /**
     * Set the key of custom payload.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function setCustomKey($key)
    {
        $this->customKey = $key;

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
     * @return int|null
     */
    public function code($response)
    {
        if (is_array($response)) {
            return array_get($response, 'ret_code');
        }
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
     * @return mixed|null
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
     * @return array
     */
    public function encodeCustomData($data)
    {
        if ($this->customKey && $data) {
            return [$this->customKey => $data];
        }

        return $data ?: [];
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
        $message->setCustom($this->encodeCustomData($custom));
        if (is_int($badge) && $badge >= 0) {
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
     * @param  string  $title
     * @param  string  $content
     * @param  mixed  $custom
     * @param  int  $type
     * @return \ElfSundae\XgPush\Message
     */
    public function createAndroidMessage($title = '', $content = '', $custom = null, $type = Message::TYPE_MESSAGE)
    {
        $message = new Message();
        $message->setTitle($title);
        $message->setContent($content);
        $message->setCustom($this->encodeCustomData($custom));
        $message->setType($type);

        return $message;
    }

    /**
     * Create a new Message instance for notification.
     * The default action is opening app.
     *
     * @param  string  $title
     * @param  string  $content
     * @param  mixed  $custom
     * @return \ElfSundae\XgPush\Message
     */
    public function createAndroidNotification($title = '', $content = '', $custom = null)
    {
        $message = $this->createAndroidMessage($title, $content, $custom, Message::TYPE_NOTIFICATION);

        $message->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $message->setAction($action);

        return $message;
    }

    /**
     * Push message to a device.
     *
     * @param  string  $deviceToken
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return array
     */
    public function toDevice($deviceToken, $message)
    {
        return $this->xinge->PushSingleDevice($deviceToken, $message, $this->environment);
    }

    /**
     * Push message to all devices.
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return array
     */
    public function toAllDevices($message)
    {
        return $this->xinge->PushAllDevices(0, $message, $this->environment);
    }

    /**
     * Push message to an user.
     *
     * @param  mixed  $user
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return array
     */
    public function toUser($user, $message)
    {
        return $this->xinge->PushSingleAccount(0, $this->accountForUser($user), $message, $this->environment);
    }

    /**
     * Push message to multi users.
     *
     * @warning 用户数限制 100 个。
     *
     * @param  string[]  $users
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return array
     */
    public function toUsers($users, $message)
    {
        $accounts = array_map([$this, 'accountForUser'], (array) $users);

        return $this->xinge->PushAccountList(0, $accounts, $message, $this->environment);
    }

    /**
     * Push message to tagged devices.
     *
     * @param  string|string[]  $tags
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @param  string  $tagsOperation  'OR', 'AND'
     * @return array
     */
    public function toTags($tags, $message, $tagsOperation = 'OR')
    {
        return $this->xinge->PushTags(0, (array) $tags, strtoupper($tagsOperation), $message, $this->environment);
    }

    /**
     * Create a batch push.
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return string|null
     */
    public function createBatch($message)
    {
        return $this->result($this->xinge->CreateMultipush($message, $this->environment), 'push_id');
    }

    /**
     * Batch pushing to a list of users.
     *
     * @warning 用户数限制 1000 个。
     *
     * @param  int|string  $pushId
     * @param  string|string[] $users
     * @return array
     */
    public function batchToUsers($pushId, $users)
    {
        $accounts = array_map([$this, 'accountForUser'], (array) $users);

        return $this->xinge->PushAccountListMultiple($pushId, $accounts);
    }

    /**
     * Batch pushing to a list of devices.
     *
     * @param  int|string  $pushId
     * @param  string|string[]  $devices
     * @return array
     */
    public function batchToDevices($pushId, $devices)
    {
        return $this->xinge->PushDeviceListMultiple($pushId, (array) $devices);
    }

    /**
     * Query group pushing status.
     *
     * @param  string|string[]  $pushIds
     * @return array
     */
    public function queryPushStatus($pushIds)
    {
        if (! is_array($pushIds)) {
            $pushIds = func_get_args();
        }

        $list = $this->result($this->xinge->QueryPushStatus($pushIds), 'list') ?: [];

        return array_combine(array_pluck($list, 'push_id'), $list);
    }

    /**
     * Query all device tokens for the given user.
     *
     * @param  mixed  $user
     * @return string[]|null
     */
    public function queryDeviceTokensForUser($user)
    {
        return $this->result($this->xinge->QueryTokensOfAccount($this->accountForUser($user)), 'tokens');
    }

    /**
     * Query all tags for the given device token.
     *
     * @param  string  $deviceToken
     * @return string[]|null
     */
    public function queryTagsForDeviceToken($deviceToken)
    {
        return $this->result($this->xinge->QueryTokenTags($deviceToken), 'tags');
    }

    /**
     * Query all tags for the given user.
     *
     * @param  mixed  $user
     * @param  array  &$deviceTokens
     * @return array|null
     */
    public function queryTagsForUser($user, &$deviceTokens = null)
    {
        $deviceTokens = $this->queryDeviceTokensForUser($user);

        if (is_array($deviceTokens)) {
            $result = [];
            foreach ($deviceTokens as $token) {
                $result[$token] = $this->queryTagsForDeviceToken($token) ?: [];
            }

            return $result;
        }
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
