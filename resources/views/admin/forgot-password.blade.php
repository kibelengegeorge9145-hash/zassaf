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
    <title>{{ __('admin.forgot_password') }} · Zassaf</title>
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
            <h1 class="login-title">{{ __('admin.forgot_password') }}</h1>
            <p class="form-card-sub">{{ __('admin.forgot_password_subtitle') }}</p>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf

                <div class="form-field">
                    <label for="email">{{ __('admin.email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-gold btn-block">{{ __('admin.send_reset_link') }}</button>
            </form>

            <p class="login-alt"><a href="{{ route('admin.login') }}">{{ __('admin.back_to_login') }}</a></p>
        </div>

        <a href="{{ route('home') }}" class="login-back">{{ __('admin.back_to_site') }}</a>
    </div>
</body>
</html>
