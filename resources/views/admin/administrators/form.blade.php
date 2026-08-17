@php
    $editing = isset($user) && $user->exists;
    $isSelf = $editing && auth()->user()->is($user);
    $originalRole = $editing ? $user->getOriginal('role') : null;
@endphp

<x-layouts.admin :title="$editing ? __('admin.administrators.edit') : __('admin.administrators.create')">
    <div class="admin-section">
        <h2>{{ $editing ? __('admin.administrators.edit') : __('admin.administrators.add_admin') }}</h2>
        <p class="admin-sub">{{ $editing ? __('admin.administrators.edit_sub') : __('admin.administrators.create_sub') }}</p>
    </div>

    <form method="POST"
          action="{{ $editing ? route('admin.administrators.update', $user) : route('admin.administrators.store') }}"
          class="form-card admin-form"
          @if ($editing && ! $isSelf)
              data-role-warning="{{ __('admin.administrators.role_warning') }}"
          @endif>
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-field">
                <label for="name">{{ __('admin.administrators.full_name') }} <span class="req">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required maxlength="120" autocomplete="name">
                @error('name') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="username">{{ __('admin.administrators.username') }} <span class="req">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username ?? '') }}" required maxlength="40" autocomplete="username">
                @error('username') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="email">{{ __('admin.administrators.email') }} <span class="req">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required maxlength="190" autocomplete="email">
                @error('email') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="phone">{{ __('admin.administrators.phone') }}</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" maxlength="30" autocomplete="tel">
                @error('phone') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="password">{{ __('admin.administrators.password') }} <span class="req">{{ $editing ? '' : '*' }}</span></label>
                <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
                @if ($editing)
                    <small class="field-hint">{{ __('admin.administrators.password_hint') }}</small>
                @endif
            </div>

            <div class="form-field">
                <label for="password_confirmation">{{ __('admin.administrators.confirm_password') }} <span class="req">{{ $editing ? '' : '*' }}</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" {{ $editing ? '' : 'required' }}>
            </div>

            <div class="form-field">
                <label for="role">{{ __('admin.administrators.role') }} <span class="req">*</span></label>
                @if ($isSelf)
                    <input type="text" value="{{ $user->role_label }}" disabled>
                    <input type="hidden" name="role" value="{{ $user->role }}">
                @else
                    <select id="role" name="role" required data-role-original="{{ $originalRole ?? '' }}">
                        @foreach (\App\Models\User::ROLES as $role)
                            <option value="{{ $role }}" @selected(old('role', $user->role ?? \App\Models\User::ROLE_EDITOR) === $role)>
                                {{ __('admin.roles.' . $role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $user->is_active ?? true))
                           {{ $isSelf ? 'disabled' : '' }}>
                    <span>{{ __('admin.administrators.status') }}: {{ __('admin.administrators.active') }}</span>
                </label>
                @if ($isSelf)
                    <input type="hidden" name="is_active" value="1">
                    <small class="field-hint">{{ __('admin.administrators.self_active_hint') }}</small>
                @endif
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ $editing ? __('admin.actions.save') : __('admin.administrators.create_button') }}</button>
            <a href="{{ route('admin.administrators.index') }}" class="btn btn-ghost">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
</x-layouts.admin>
