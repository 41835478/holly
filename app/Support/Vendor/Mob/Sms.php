<?php

namespace App\Support\Vendor\Mob;

use App\Exceptions\ActionFailureException;
use App\Exceptions\InvalidInputException;
use Exception;

class Sms
{
    /**
     * MobSMS 短信验证码。
     *
     * @see http://wiki.mob.com/webapi2-0/
     *
     * @param  array  $credentials  `['phone', 'code', 'zone']`
     *
     * @throws \App\Exceptions\InvalidInputException
     * @throws \App\Exceptions\ActionFailureException
     */
    public static function verify($credentials = [])
    {
        $credentials['appkey'] = config('services.mobsms.key');

        try {
            $response = (new HttpClient([
                'connect_timeout' => 5,
                'timeout' => 20,
            ]))
            ->post('https://webapi.sms.mob.com/sms/verify', [
                'form_params' => $credentials,
            ])
            ->getBody();

            $response = json_decode($response, true);
        } catch (Exception $e) {
            throw new ActionFailureException('短信网关请求失败');
        }

        if (! is_array($response)) {
            throw new ActionFailureException('短信网关数据异常[sms]');
        }

        $status = (int) array_get($response, 'status', -1);

        if (200 === $status) {
            return;
        }

        if (457 === $status) {
            throw new InvalidInputException('请填写正确的手机号', 10);
        } elseif (467 === $status) {
            throw new InvalidInputException('请求验证码过于频繁，请稍后再试');
        } else {
            throw new InvalidInputException('验证码无效，请重新获取');
        }
    }
}
