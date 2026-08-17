<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Carbon;

/**
 * Reads the operational membership configuration from the key-value settings
 * store. All values are editable by an admin from the Membership Settings page
 * and are never hard-coded into controllers or templates.
 */
class MembershipConfig
{
    public const STATUS_COMING_SOON = 'coming_soon';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_COMING_SOON,
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
    ];

    public static function status(): string
    {
        return Setting::value('membership_status', self::STATUS_COMING_SOON);
    }

    public static function isOpen(): bool
    {
        return self::status() === self::STATUS_OPEN;
    }

    public static function launchDate(): ?Carbon
    {
        $value = Setting::value('membership_launch_date');

        return $value ? Carbon::parse($value) : null;
    }

    public static function registrationFee(): float
    {
        return (float) Setting::value('membership_registration_fee', 10000);
    }

    public static function monthlyFee(): float
    {
        return (float) Setting::value('membership_monthly_fee', 5000);
    }

    public static function currency(): string
    {
        return Setting::value('membership_currency', 'TZS');
    }

    public static function registrationOpen(): bool
    {
        return self::isOpen()
            && (bool) Setting::value('membership_registration_open', false);
    }

    public static function paymentEnabled(): bool
    {
        return (bool) Setting::value('membership_payment_enabled', false);
    }

    public static function formattedRegistrationFee(): string
    {
        return number_format(self::registrationFee(), 0).' '.self::currency();
    }

    public static function formattedMonthlyFee(): string
    {
        return number_format(self::monthlyFee(), 0).' '.self::currency();
    }

    /**
     * Effective launch label for marketing copy, e.g. "January 2027".
     */
    public static function launchLabel(): string
    {
        $date = self::launchDate();

        return $date ? $date->format('F Y') : __('membership.launch_default');
    }
}
