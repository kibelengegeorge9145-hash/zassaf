<x-layouts.admin :title="__('admin.settings.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.settings.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.settings.index_sub') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="form-card admin-form">
        @csrf
        @method('PUT')

        <h3 class="admin-form-heading">{{ __('admin.settings.fields.organization') }}</h3>
        <div class="form-grid">
            <div class="form-field">
                <label for="org_name">{{ __('admin.settings.fields.org_name') }}</label>
                <input type="text" id="org_name" name="org_name" value="{{ old('org_name', $settings['org_name'] ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label for="motto">{{ __('admin.settings.fields.motto') }}</label>
                <input type="text" id="motto" name="motto" value="{{ old('motto', $settings['motto'] ?? '') }}" maxlength="255">
            </div>

            <div class="form-field form-field-full">
                <label for="tagline">{{ __('admin.settings.fields.tagline') }}</label>
                <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}" maxlength="255">
            </div>

            <div class="form-field form-field-full">
                <label for="logo">{{ __('admin.settings.fields.logo') }}</label>
                <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/svg+xml,image/webp">
                <p class="field-hint">{{ __('admin.settings.fields.logo_hint') }}</p>
                @error('logo') <span class="field-error">{{ $message }}</span> @enderror

                @php
                    $logoPreviewPath = $settings['logo_path'] ?? '';
                @endphp

                @if ($logoPreviewPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPreviewPath))
                    <div class="logo-preview">
                        <img src="{{ asset('storage/' . $logoPreviewPath) }}" alt="{{ __('admin.settings.fields.current_logo') }}">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remove_logo" value="1">
                            <span>{{ __('admin.settings.fields.remove_logo') }} — {{ __('admin.settings.fields.remove_logo_hint') }}</span>
                        </label>
                    </div>
                @endif
            </div>
        </div>

        <h3 class="admin-form-heading">{{ __('admin.settings.fields.contact') }}</h3>
        <div class="form-grid">
            <div class="form-field">
                <label for="contact_phone">{{ __('admin.settings.fields.contact_phone') }}</label>
                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" maxlength="30">
            </div>

            <div class="form-field">
                <label for="contact_email">{{ __('admin.settings.fields.contact_email') }}</label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" maxlength="190">
            </div>

            <div class="form-field form-field-full">
                <label for="contact_address">{{ __('admin.settings.fields.contact_address') }}</label>
                <input type="text" id="contact_address" name="contact_address" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}" maxlength="255">
            </div>
        </div>

        <h3 class="admin-form-heading">{{ __('admin.settings.fields.social') }}</h3>
        <div class="form-grid">
            <div class="form-field">
                <label for="whatsapp_url">{{ __('admin.settings.fields.whatsapp_url') }}</label>
                <input type="url" id="whatsapp_url" name="whatsapp_url" value="{{ old('whatsapp_url', $settings['whatsapp_url'] ?? '') }}" maxlength="500" placeholder="https://wa.me/2557XXXXXXX">
                @error('whatsapp_url') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="email">{{ __('admin.settings.fields.email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" maxlength="190">
                @error('email') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="instagram_url">{{ __('admin.settings.fields.instagram_url') }}</label>
                <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" maxlength="500" placeholder="https://instagram.com/...">
                @error('instagram_url') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="facebook_url">{{ __('admin.settings.fields.facebook_url') }}</label>
                <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}" maxlength="500" placeholder="https://facebook.com/...">
                @error('facebook_url') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="tiktok_url">{{ __('admin.settings.fields.tiktok_url') }}</label>
                <input type="url" id="tiktok_url" name="tiktok_url" value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}" maxlength="500" placeholder="https://tiktok.com/@...">
                @error('tiktok_url') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="telegram_url">{{ __('admin.settings.fields.telegram_url') }}</label>
                <input type="url" id="telegram_url" name="telegram_url" value="{{ old('telegram_url', $settings['telegram_url'] ?? '') }}" maxlength="500" placeholder="https://t.me/...">
                @error('telegram_url') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
        </div>
    </form>
</x-layouts.admin>
