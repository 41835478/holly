<?php

namespace App\Http;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class FormRequest extends BaseFormRequest
{
    /**
     * Handle a failed authorization attempt.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException('Forbidden');
    }
}
