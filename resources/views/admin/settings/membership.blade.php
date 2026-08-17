<x-layouts.admin :title="__('admin.membership_settings.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.membership_settings.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.membership_settings.index_sub') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.membership_settings.update') }}" class="form-card admin-form">
        @csrf
        @method('PUT')

        <h3 class="admin-form-heading">{{ __('admin.membership_settings.general') }}</h3>
        <div class="form-grid">
            <div class="form-field">
                <label for="membership_status">{{ __('admin.membership_settings.status_label') }}</label>
                <select id="membership_status" name="membership_status">
                    <option value="coming_soon" @selected(($settings['membership_status'] ?? 'coming_soon') === 'coming_soon')>{{ __('admin.membership_settings.status_coming_soon') }}</option>
                    <option value="open" @selected(($settings['membership_status'] ?? '') === 'open')>{{ __('admin.membership_settings.status_open') }}</option>
                    <option value="closed" @selected(($settings['membership_status'] ?? '') === 'closed')>{{ __('admin.membership_settings.status_closed') }}</option>
                </select>
                @error('membership_status') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="membership_launch_date">{{ __('admin.membership_settings.launch_date') }}</label>
                <input type="date" id="membership_launch_date" name="membership_launch_date" value="{{ old('membership_launch_date', $settings['membership_launch_date'] ?? '2027-01-01') }}">
                @error('membership_launch_date') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </div>

        <h3 class="admin-form-heading">{{ __('admin.membership_settings.fees') }}</h3>
        <div class="form-grid">
            <div class="form-field">
                <label for="membership_registration_fee">{{ __('admin.membership_settings.registration_fee') }}</label>
                <input type="number" id="membership_registration_fee" name="membership_registration_fee" min="0" step="0.01" value="{{ old('membership_registration_fee', $settings['membership_registration_fee'] ?? '10000') }}">
                @error('membership_registration_fee') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="membership_monthly_fee">{{ __('admin.membership_settings.monthly_fee') }}</label>
                <input type="number" id="membership_monthly_fee" name="membership_monthly_fee" min="0" step="0.01" value="{{ old('membership_monthly_fee', $settings['membership_monthly_fee'] ?? '5000') }}">
                @error('membership_monthly_fee') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="membership_currency">{{ __('admin.membership_settings.currency') }}</label>
                <input type="text" id="membership_currency" name="membership_currency" value="{{ old('membership_currency', $settings['membership_currency'] ?? 'TZS') }}" maxlength="10">
                @error('membership_currency') <span class="field-error">{{ $message }}</span> @enderror
            </div>
        </div>

        <h3 class="admin-form-heading">{{ __('admin.membership_settings.flags') }}</h3>
        <div class="form-grid">
            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="membership_registration_open" value="1" @checked(($settings['membership_registration_open'] ?? '0') === '1')>
                    <span>{{ __('admin.membership_settings.registration_open') }}</span>
                </label>
                <p class="field-hint">{{ __('admin.membership_settings.registration_open_hint') }}</p>
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="membership_payment_enabled" value="1" @checked(($settings['membership_payment_enabled'] ?? '0') === '1')>
                    <span>{{ __('admin.membership_settings.payment_enabled') }}</span>
                </label>
                <p class="field-hint">{{ __('admin.membership_settings.payment_enabled_hint') }}</p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
        </div>
    </form>
</x-layouts.admin>
