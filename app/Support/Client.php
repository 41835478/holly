<?php

namespace App\Support;

use Jenssegers\Agent\Agent;
use Illuminate\Support\Fluent;
use App\Support\Traits\FluentArrayAccess;

/**
 * The app client.
 *
 * @property string os
 * @property string osVersion
 * @property string platform
 * @property string locale
 * @property bool isIOS
 * @property bool isAndroid
 * @property bool isWechat
 * @property bool isApiClient
 * @property string network
 * @property string app
 * @property string appVersion
 * @property string appChannel
 * @property string tdid
 * @property bool isAppStoreChannel
 * @property bool isDebugChannel
 * @property bool isAdHocChannel
 * @property bool isInHouseChannel
 * @property bool isAppStoreReviewing
 */
class Client extends Fluent
{
    use FluentArrayAccess;

    /**
     * The Agent instance.
     *
     * @var \Jenssegers\Agent\Agent
     */
    protected $agent;

    /**
     * Create a new Client instance.
     *
     * @param  array|object  $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->setAgent(app('agent'));
    }

    /**
     * Merge new data into the current attributes.
     *
     * @param  array  ...$data
     * @return $this
     */
    public function add(array ...$data)
    {
        $this->attributes = array_replace($this->attributes, ...$data);

        return $this;
    }

    /**
     * Check the version of the given property in the User-Agent.
     *
     * @param  string  $propertyName
     * @return string|float|false
     *
     * @see \Jenssegers\Agent\Agent::version()
     */
    public function version($propertyName, $type = Agent::VERSION_TYPE_STRING)
    {
        $version = $this->agent->version($propertyName, $type);

        return is_string($version) ? str_replace(['_', '+'], '.', $version) : $version;
    }

    /**
     * Get the Agent instance.
     *
     * @return \Jenssegers\Agent\Agent
     */
    public function agent()
    {
        return $this->agent;
    }

    /**
     * Set the Agent instance.
     *
     * @param  \Jenssegers\Agent\Agent  $agent
     * @return $this
     */
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;

        return $this->parseAgent();
    }

    /**
     * Set the User-Agent to be used.
     *
     * @param  string  $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent = null)
    {
        $this->agent->setUserAgent($userAgent);

        return $this->parseAgent();
    }

    /**
     * Parse Agent information.
     *
     * @return $this
     */
    public function parseAgent()
    {
        $this->add(
            $this->parseCommonClient(),
            $this->parseApiClient()
        );
    }

    /**
     * Parse common client.
     *
     * @return array
     */
    protected function parseCommonClient()
    {
        $info = [
            'os' => $this->agent->platform(),
            'osVersion' => $this->version($this->agent->platform()),
            'platform' => $this->agent->device(),
            'locale' => head($this->agent->languages()),
        ];

        if ((bool) $this->agent->is('iOS')) {
            $info['isIOS'] = true;
        }

        if ((bool) $this->agent->is('AndroidOS')) {
            $info['isAndroid'] = true;
            $info['os'] = 'Android';
        }

        if (is_string($this->version('MicroMessenger'))) {
            $info['isWechat'] = true;
        }

        return array_filter($info);
    }

    /**
     * Parse API client from the User-Agent.
     *
     * @example `Mozilla/5.0 (iPhone; CPU iPhone OS 8_4 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Mobile/12H143 _ua(eyJuZXQiOiJXaUZpIiwib3MiOiJpT1MiLCJhcHBWIjoiMC4xLjIiLCJvc1YiOiI4LjQiLCJhcHAiOiJndXBpYW8iLCJhcHBDIjoiRGVidWciLCJ0ZGlkIjoiaDNiYjFmNTBhYzBhMzdkYmE4ODhlMTgyNjU3OWJkZmZmIiwiYWNpZCI6IjIxZDNmYmQzNDNmMjViYmI0MzU2ZGEyMmJmZjUxZDczZjg0YWQwNmQiLCJsb2MiOiJ6aF9DTiIsInBmIjoiaVBob25lNywxIn0)`
     *
     * @return array
     */
    protected function parseApiClient()
    {
        return $this->getApiClientAttributes(
            $this->getApiClientInfo($this->agent->getUserAgent())
        );
    }

    /**
     * Get API client information from the User-Agent.
     *
     * @param  string  $userAgent
     * @return array
     */
    protected function getApiClientInfo($userAgent)
    {
        if (preg_match('#ua\((.+)\)#is', $userAgent, $matches)) {
            if ($info = json_decode(urlsafe_base64_decode($matches[1]), true)) {
                if (is_array($info) && count($info) > 0) {
                    return $info;
                }
            }
        }

        return [];
    }

    /**
     * Get API client attributes.
     *
     * @param  array  $info
     * @return array
     */
    protected function getApiClientAttributes($info)
    {
        $info = array_filter($info);
        $data = [];

        if (
            ($data['os'] = array_get($info, 'os')) &&
            ($data['osVersion'] = array_get($info, 'osV')) &&
            ($data['platform'] = array_get($info, 'pf')) &&
            ($data['locale'] = array_get($info, 'loc')) &&
            ($data['network'] = array_get($info, 'net')) &&
            ($data['app'] = array_get($info, 'app')) &&
            ($data['appVersion'] = array_get($info, 'appV')) &&
            ($data['appChannel'] = array_get($info, 'appC')) &&
            ($data['tdid'] = array_get($info, 'tdid'))
        ) {
            if ($data['os'] === 'iPhone OS') {
                $data['os'] = 'iOS';
            }

            $data['isIOS'] = $data['os'] === 'iOS';
            $data['isAndroid'] = $data['os'] === 'Android';

            $data['isAppStoreChannel'] = $data['appChannel'] === 'App Store';
            $data['isDebugChannel'] = $data['appChannel'] === 'Debug';
            $data['isAdHocChannel'] = $data['appChannel'] === 'Ad Hoc';
            $data['isInHouseChannel'] = $data['appChannel'] === 'In House';

            $data['isAppStoreReviewing'] = (
                $data['isIOS'] &&
                $data['isAppStoreChannel'] &&
                $data['appVersion'] === config('var.ios.app_store_reviewing_version')
            );

            $data['isApiClient'] = true;

            return array_filter($data);
        }

        $this->resetApiClientAttributes();

        return [];
    }

    /**
     * Reset API client attributes.
     */
    protected function resetApiClientAttributes()
    {
        unset(
            $this->attributes['network'],
            $this->attributes['app'],
            $this->attributes['appVersion'],
            $this->attributes['appChannel'],
            $this->attributes['tdid'],
            $this->attributes['isAppStoreChannel'],
            $this->attributes['isDebugChannel'],
            $this->attributes['isAdHocChannel'],
            $this->attributes['isInHouseChannel'],
            $this->attributes['isAppStoreReviewing']
        );
    }

    /**
     * Handle dynamic calls to the Agent instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->agent, $method], $parameters);
    }
}
