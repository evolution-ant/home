<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Http\Request;
use Encore\Admin\Controllers\AuthController as BaseAuthController;

class AutoLoginController extends BaseAuthController
{
    public function autoLogin(Request $request)
    {
        \Log::info(__METHOD__, ['enter']);
        try {
            $user = Administrator::where("id", 2)->first();
            $this->guard()->login($user);
            return redirect()->intended("admin");
        } catch (\Exception $e) {
            \Log::info(__METHOD__, ["error:", $e]);
            return redirect('/admin/auth/login');
        }
    }
}
