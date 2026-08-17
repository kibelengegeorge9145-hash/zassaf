<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => [
                'nullable', 'string', 'max:40', 'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required', 'email', 'max:190',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        if (blank($data['username'] ?? null)) {
            unset($data['username']);
        }

        $user->update($data);

        AuditLog::log(
            AuditLog::ACTION_PROFILE_UPDATED,
            __('admin.audit.profile_updated', ['actor' => $user->name]),
            $user
        );

        return redirect()->route('admin.profile')
            ->with('success', __('admin.profile.updated_success'));
    }

    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->update([
            'profile_photo' => $request->file('profile_photo')->store('avatars', 'public'),
        ]);

        AuditLog::log(
            AuditLog::ACTION_PROFILE_UPDATED,
            __('admin.audit.profile_updated', ['actor' => $user->name]),
            $user
        );

        return redirect()->route('admin.profile')
            ->with('success', __('admin.profile.photo_updated'));
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => __('admin.profile.current_password_incorrect')])
                ->with('error', __('admin.profile.current_password_incorrect'));
        }

        $user->update(['password' => $data['password']]);

        AuditLog::log(
            AuditLog::ACTION_PASSWORD_CHANGED,
            __('admin.audit.password_changed', ['actor' => $user->name]),
            $user
        );

        return redirect()->route('admin.profile', '#security')
            ->with('success', __('admin.profile.password_changed_success'));
    }
}
