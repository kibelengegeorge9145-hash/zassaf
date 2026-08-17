<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private const TEXT_KEYS = [
        'org_name',
        'motto',
        'tagline',
        'contact_phone',
        'contact_email',
        'contact_address',
        'whatsapp_url',
        'email',
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'telegram_url',
    ];

    public function edit()
    {
        $settings = Setting::allCached();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'org_name' => ['nullable', 'string', 'max:255'],
            'motto' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'whatsapp_url' => ['nullable', 'url', 'max:500'],
            'email' => ['nullable', 'email', 'max:190'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'tiktok_url' => ['nullable', 'url', 'max:500'],
            'telegram_url' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        foreach (self::TEXT_KEYS as $key) {
            Setting::set($key, (string) ($validated[$key] ?? ''));
        }

        if ($request->boolean('remove_logo')) {
            $existing = Setting::value('logo_path');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            Setting::set('logo_path', '');
        }

        if ($request->hasFile('logo')) {
            $existing = Setting::value('logo_path');
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }

            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo_path', $path);
        }

        AuditLog::log(
            AuditLog::ACTION_SETTINGS_CHANGED,
            __('admin.audit.settings_changed', ['actor' => $request->user()->name])
        );

        return back()->with('success', __('admin.settings.saved'));
    }
}
