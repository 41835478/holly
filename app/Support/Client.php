<?php

namespace App\Support;

use Holly\Support\Client as BaseClient;

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
class Client extends BaseClient
{
    /**
     * Parse API client from the User-Agent.
     *
     * @example `Mozilla/5.0 (iPhone; CPU iPhone OS 8_4 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Mobile/12H143 _ua(eyJuZXQiOiJXaUZpIiwib3MiOiJpT1MiLCJhcHBWIjoiMC4xLjIiLCJvc1YiOiI4LjQiLCJhcHAiOiJndXBpYW8iLCJhcHBDIjoiRGVidWciLCJ0ZGlkIjoiaDNiYjFmNTBhYzBhMzdkYmE4ODhlMTgyNjU3OWJkZmZmIiwiYWNpZCI6IjIxZDNmYmQzNDNmMjViYmI0MzU2ZGEyMmJmZjUxZDczZjg0YWQwNmQiLCJsb2MiOiJ6aF9DTiIsInBmIjoiaVBob25lNywxIn0)`
     *
     * @param  string  $userAgent
     * @return bool
     */
    protected function parseApiClient($userAgent)
    {
        if ($info = $this->getApiClientInfo($userAgent)) {
            if (true === $this->setApiClientAttributes($info)) {
                return true;
            }
        }

        $this->resetApiClientAttributes();

        return false;
    }

    /**
     * Get API client information from the User-Agent.
     *
     * @param  string  $userAgent
     * @return array|null
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
    }

    /**
     * Set API client attributes.
     *
     * @param  array  $info
     * @return bool
     */
    protected function setApiClientAttributes($info)
    {
        $info = array_filter($info);

        if (
            ($this->os = array_get($info, 'os')) &&
            ($this->osVersion = array_get($info, 'osV')) &&
            ($this->platform = array_get($info, 'pf')) &&
            ($this->locale = array_get($info, 'loc')) &&
            ($this->network = array_get($info, 'net')) &&
            ($this->app = array_get($info, 'app')) &&
            ($this->appVersion = array_get($info, 'appV')) &&
            ($this->appChannel = array_get($info, 'appC')) &&
            ($this->tdid = array_get($info, 'tdid'))
        ) {
            if ($this->os == 'iPhone OS') {
                $this->os = 'iOS';
            }

            $this->isIOS = ($this->os == 'iOS');
            $this->isAndroid = ($this->os == 'Android');

            $this->isAppStoreChannel = ($this->appChannel === 'App Store');
            $this->isDebugChannel = ($this->appChannel === 'Debug');
            $this->isAdHocChannel = ($this->appChannel === 'Ad Hoc');
            $this->isInHouseChannel = ($this->appChannel === 'In House');

            $this->isAppStoreReviewing = (
                $this->isIOS &&
                $this->isAppStoreChannel &&
                $this->appVersion === config('var.ios.app_store_reviewing_version')
            );

            return true;
        }

        return false;
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
}
