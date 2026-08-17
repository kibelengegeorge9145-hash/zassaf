<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipSettingController extends Controller
{
    private const KEYS = [
        'membership_status',
        'membership_launch_date',
        'membership_registration_fee',
        'membership_monthly_fee',
        'membership_currency',
        'membership_registration_open',
        'membership_payment_enabled',
    ];

    public function edit()
    {
        $settings = Setting::allCached();

        return view('admin.settings.membership', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'membership_status' => ['required', 'string', Rule::in(['coming_soon', 'open', 'closed'])],
            'membership_launch_date' => ['required', 'date'],
            'membership_registration_fee' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'membership_monthly_fee' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'membership_currency' => ['required', 'string', 'max:10'],
            'membership_registration_open' => ['nullable', 'boolean'],
            'membership_payment_enabled' => ['nullable', 'boolean'],
        ]);

        Setting::set('membership_status', $validated['membership_status']);
        Setting::set('membership_launch_date', $validated['membership_launch_date']);
        Setting::set('membership_registration_fee', $validated['membership_registration_fee']);
        Setting::set('membership_monthly_fee', $validated['membership_monthly_fee']);
        Setting::set('membership_currency', $validated['membership_currency']);
        Setting::set('membership_registration_open', $request->boolean('membership_registration_open') ? '1' : '0');
        Setting::set('membership_payment_enabled', $request->boolean('membership_payment_enabled') ? '1' : '0');

        AuditLog::log(
            AuditLog::ACTION_MEMBERSHIP_SETTINGS_CHANGED,
            __('admin.audit.membership_settings_changed', ['actor' => $request->user()->name])
        );

        return back()->with('success', __('admin.membership_settings.saved'));
    }
}
