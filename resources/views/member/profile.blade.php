<x-layouts.member :title="__('membership.profile.heading')">
    <div class="admin-section">
        <h2>{{ __('membership.profile.heading') }}</h2>
        <p class="admin-sub">{{ __('membership.profile.sub') }}</p>
    </div>

    <div class="admin-columns">
        <form method="POST" action="{{ route('member.profile.update') }}" class="form-card admin-form">
            @csrf
            @method('PUT')

            <h3 class="admin-form-heading">{{ __('membership.profile.personal') }}</h3>
            <div class="form-grid">
                <div class="form-field">
                    <label for="name">{{ __('membership.profile.name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="email">{{ __('membership.profile.email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="190">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="phone">{{ __('membership.profile.phone') }}</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30">
                    @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="gender">{{ __('membership.profile.gender') }}</label>
                    <select id="gender" name="gender">
                        <option value="">—</option>
                        <option value="male" @selected(old('gender', $user->gender) === 'male')>{{ __('membership.register.gender_male') }}</option>
                        <option value="female" @selected(old('gender', $user->gender) === 'female')>{{ __('membership.register.gender_female') }}</option>
                        <option value="other" @selected(old('gender', $user->gender) === 'other')>{{ __('membership.register.gender_other') }}</option>
                    </select>
                    @error('gender') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="date_of_birth">{{ __('membership.profile.date_of_birth') }}</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}">
                    @error('date_of_birth') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="location">{{ __('membership.profile.location') }}</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $user->location) }}" maxlength="255">
                    @error('location') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">{{ __('membership.profile.save') }}</button>
            </div>
        </form>

        <form method="POST" action="{{ route('member.profile.password') }}" class="form-card admin-form">
            @csrf
            @method('PUT')

            <h3 class="admin-form-heading">{{ __('membership.profile.security') }}</h3>
            <div class="form-grid">
                <div class="form-field form-field-full">
                    <label for="current_password">{{ __('membership.profile.current_password') }}</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    @error('current_password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="new_password">{{ __('membership.profile.new_password') }}</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
                    @error('new_password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="new_password_confirmation">{{ __('membership.profile.confirm_password') }}</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8" autocomplete="new-password">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">{{ __('membership.profile.change_password') }}</button>
            </div>
        </form>
    </div>
</x-layouts.member>
