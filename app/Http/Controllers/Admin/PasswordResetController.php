<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        if (Auth::check() && Auth::user()->canAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                if (! $user->canAdmin()) {
                    return;
                }

                $user->sendPasswordResetNotification($token);
            }
        );

        return back()->with('status', $status);
    }

    public function showResetForm(Request $request, string $token)
    {
        if (Auth::check() && Auth::user()->canAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $credentials = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset($credentials, function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));

            Auth::guard()->logout();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', $status)
            : back()->withErrors(['email' => $status])->withInput(['email' => $credentials['email']]);
    }
}
