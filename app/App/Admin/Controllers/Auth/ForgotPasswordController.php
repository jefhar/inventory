<?php

namespace App\Admin\Controllers\Auth;

use App\Admin\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

/**
 * Class ForgotPasswordController
 *
 * @package App\Admin\Controllers\Auth
 * @codeCoverageIgnore
 */
class ForgotPasswordController extends Controller
{
    /**
     * |--------------------------------------------------------------------------
     * | Password Reset Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller is responsible for handling password reset emails and
     * | includes a trait which assists in sending these notifications from
     * | your application to your users. Feel free to explore this trait.
     * |
     */
    use SendsPasswordResetEmails;
}
