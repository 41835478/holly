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
}
