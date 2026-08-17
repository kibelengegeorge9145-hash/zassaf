<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::value($key, $default);
    }
}

if (! function_exists('localized_motto')) {
    function localized_motto(): string
    {
        return app()->getLocale() === 'sw'
            ? __('site.motto_sw')
            : __('site.motto');
    }
}
