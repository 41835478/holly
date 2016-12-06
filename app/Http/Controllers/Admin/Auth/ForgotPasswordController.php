<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Support\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('admin.auth.passwords');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'captcha' => 'required|captcha',
        ]);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response === Password::RESET_LINK_SENT) {
            $emailLoginPage = Helper::mailHomepage($request['email']);

            $checkOutEmailMessage = "<a href='#'
                onclick='(window.open(\"$emailLoginPage\", \"_blank\")).focus();
                window.location=\"/login\";'>请前往邮箱查收。</a>";

            return api(trans($response).$checkOutEmailMessage);
        }

        return api(trans($response), 510);
    }
}
