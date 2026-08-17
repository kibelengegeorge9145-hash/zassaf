@php
    $orgName = setting('org_name', 'Zassaf Elite Community');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('admin.reset_password') }} · Zassaf</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-body">
    <div class="login-wrap">
        <a href="{{ route('home') }}" class="brand login-brand">
            <span class="brand-mark" aria-hidden="true">Z</span>
            <span class="brand-text">
                <span class="brand-name">{{ $orgName }}</span>
                <span class="brand-sub">Think. Grow. Lead.</span>
            </span>
        </a>

        <div class="form-card login-card">
            <h1 class="login-title">{{ __('admin.reset_password') }}</h1>
            <p class="form-card-sub">{{ __('admin.reset_password_subtitle') }}</p>

            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-field">
                    <label for="email">{{ __('admin.email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" required autocomplete="username">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="password">{{ __('admin.password') }}</label>
                    <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="password_confirmation">{{ __('admin.confirm_password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-gold btn-block">{{ __('admin.reset_password') }}</button>
            </form>

            <p class="login-alt"><a href="{{ route('admin.login') }}">{{ __('admin.back_to_login') }}</a></p>
        </div>

        <a href="{{ route('home') }}" class="login-back">{{ __('admin.back_to_site') }}</a>
    </div>
</body>
</html>
