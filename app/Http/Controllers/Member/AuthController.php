<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isMember()) {
            return redirect()->route('member.dashboard');
        }

        return view('member.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(
            array_merge($credentials, [
                'is_active' => true,
                'role' => User::ROLE_MEMBER,
            ]),
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            Auth::user()->forceFill(['last_login_at' => now()])->save();

            return redirect()->intended(route('member.dashboard'));
        }

        return back()
            ->withErrors(['email' => __('membership.login.failed')])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('membership');
    }
}
