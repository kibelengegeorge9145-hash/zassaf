<x-layouts.admin :title="__('admin.profile.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.profile.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.profile.index_sub') }}</p>
    </div>

    <div class="admin-columns profile-grid">
        <div class="profile-aside">
            <div class="admin-panel">
                <h3>{{ __('admin.profile.photo_heading') }}</h3>

                <form method="POST" action="{{ route('admin.profile.photo') }}" enctype="multipart/form-data" class="admin-form">
                    @csrf

                    <div class="profile-photo">
                        <x-avatar :user="auth()->user()" class="avatar-xl" />
                    </div>

                    <div class="form-field">
                        <label for="profile_photo">{{ __('admin.profile.upload_photo') }}</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp">
                        <p class="field-hint">{{ __('admin.profile.photo_hint') }}</p>
                        @error('profile_photo') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gold btn-sm">
                            <x-icon name="plus" /> {{ __('admin.profile.upload_button') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="admin-panel">
                <h3>{{ __('admin.profile.account_heading') }}</h3>
                <dl class="detail-list">
                    <div>
                        <dt>{{ __('admin.profile.username') }}</dt>
                        <dd>{{ auth()->user()->username }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('admin.profile.role') }}</dt>
                        <dd>{{ auth()->user()->role_label }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('admin.profile.status') }}</dt>
                        <dd>
                            <span class="chip {{ auth()->user()->is_active ? 'chip-gold' : '' }}">
                                {{ auth()->user()->status_label }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt>{{ __('admin.profile.date_joined') }}</dt>
                        <dd>{{ auth()->user()->created_at?->format('j F Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('admin.profile.last_login') }}</dt>
                        <dd>{{ auth()->user()->last_login_at?->format('j F Y, H:i') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="profile-main">
            <form method="POST" action="{{ route('admin.profile.update') }}" class="form-card admin-form">
                @csrf
                @method('PUT')

                <h3 class="admin-form-heading">{{ __('admin.profile.personal_heading') }}</h3>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="name">{{ __('admin.profile.full_name') }} <span class="req">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="120" autocomplete="name">
                        @error('name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="username">{{ __('admin.profile.username') }}</label>
                        <input type="text" id="username" name="username" value="{{ old('username', auth()->user()->username) }}" maxlength="40" autocomplete="username">
                        @error('username') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="email">{{ __('admin.profile.email') }} <span class="req">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="190" autocomplete="email">
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="phone">{{ __('admin.profile.phone') }}</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" maxlength="30" autocomplete="tel">
                        @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-gold">{{ __('admin.profile.save_profile') }}</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.profile.password.update') }}" id="security" class="form-card admin-form">
                @csrf
                @method('PUT')

                <h3 class="admin-form-heading">{{ __('admin.profile.security_heading') }}</h3>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="current_password">{{ __('admin.profile.current_password') }} <span class="req">*</span></label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                        @error('current_password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="password">{{ __('admin.profile.new_password') }} <span class="req">*</span></label>
                        <input type="password" id="password" name="password" minlength="8" required autocomplete="new-password">
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">{{ __('admin.profile.confirm_password') }} <span class="req">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-gold">
                        <x-icon name="lock" /> {{ __('admin.profile.change_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
