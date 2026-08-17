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
    <title>{{ __('admin.login') }} · Zassaf</title>
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
            <h1 class="login-title">{{ __('admin.login') }}</h1>
            <p class="form-card-sub">{{ __('admin.login_subtitle') }}</p>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="form-field">
                    <label for="email">{{ __('admin.email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="password">{{ __('admin.password') }}</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>{{ __('admin.remember') }}</span>
                    </label>
                    <a href="{{ route('admin.password.request') }}" class="forgot-link">{{ __('admin.forgot_password_link') }}</a>
                </div>

                <button type="submit" class="btn btn-gold btn-block">{{ __('admin.login') }}</button>
            </form>

            <p class="login-alt"><a href="{{ route('admin.password.request') }}">{{ __('admin.forgot_password') }}</a></p>
        </div>

        <a href="{{ route('home') }}" class="login-back">{{ __('admin.back_to_site') }}</a>
    </div>
</body>
</html>
