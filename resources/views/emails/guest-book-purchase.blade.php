<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('books.emails.guest_subject') }}</title>
</head>
<body style="margin:0;padding:24px;background:#f4f1ea;font-family:Inter,Arial,sans-serif;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e8e3d8;border-radius:12px;padding:32px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
            <span style="display:inline-flex;width:40px;height:40px;border-radius:50%;background:#0b0b0d;color:#c9a227;align-items:center;justify-content:center;font-weight:800;">Z</span>
            <strong style="font-size:18px;">Zassaf Elite Community</strong>
        </div>

        <h1 style="font-size:22px;margin:0 0 8px;">{{ __('books.emails.guest_heading') }}</h1>
        <p style="color:#555;margin:0 0 24px;">{{ __('books.emails.guest_greeting', ['name' => $payment->customer_name]) }}</p>

        <ul style="list-style:none;padding:0;margin:0 0 24px;border:1px solid #eee;border-radius:8px;">
            <li style="padding:12px 16px;border-bottom:1px solid #eee;">
                <small style="color:#888;">{{ __('books.emails.book') }}</small><br>
                <strong>{{ $book->title_en ?? $book->title ?? '—' }}</strong>
            </li>
            <li style="padding:12px 16px;border-bottom:1px solid #eee;">
                <small style="color:#888;">{{ __('books.emails.amount') }}</small><br>
                <strong>{{ $amount }}</strong>
            </li>
            <li style="padding:12px 16px;">
                <small style="color:#888;">{{ __('books.emails.reference') }}</small><br>
                <strong>{{ $reference }}</strong>
            </li>
        </ul>

        <a href="{{ $downloadUrl }}" style="display:inline-block;background:#c9a227;color:#0b0b0d;font-weight:700;text-decoration:none;padding:14px 24px;border-radius:8px;">
            {{ __('books.emails.download_button') }}
        </a>

        <p style="color:#888;font-size:13px;margin:24px 0 0;">{{ __('books.emails.expiry_note') }}</p>
    </div>
</body>
</html>
