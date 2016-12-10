<?php

namespace App\Http\Requests;

use App\Support\Http\FormRequest;
use App\Support\Vendor\MobSms;

class VerifyPhoneNumber extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'bail|required|digits',
            'code' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'phone' => '手机号',
            'code' => '验证码',
        ];
    }

    /**
     * Get the credentials for verfication.
     *
     * @return array
     */
    public function getCredentials()
    {
        return array_filter($this->only('phone', 'code', 'zone')) + ['zone' => '86'];
    }

    /**
     * Get the phone number.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->input('phone');
    }

    /**
     * Verify the phone number and the code.
     *
     * @return array credentials
     *
     * @throws \App\Exceptions\InvalidInputException
     * @throws \App\Exceptions\ActionFailureException
     */
    public function verify()
    {
        $credentials = $this->getCredentials();

        if (! $this->shouldPassThrough($credentials)) {
            MobSms::verify($credentials);
        }

        return $credentials;
    }

    /**
     * Determine if the given credentials should be passed through verification.
     *
     * @return bool
     */
    protected function shouldPassThrough($credentials)
    {
        foreach (config('var.validation.verify_phone_excepts.phones') as $phone) {
            foreach (config('var.validation.verify_phone_excepts.codes') as $code) {
                if (
                    str_is($phone, $credentials['phone']) &&
                    str_is($code, $credentials['code'])
                ) {
                    return true;
                }
            }
        }

        return false;
    }
}
