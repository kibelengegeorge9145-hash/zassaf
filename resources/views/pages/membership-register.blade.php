<x-layouts.public
    :title="__('membership.register.heading')"
    :description="__('membership.register.sub')"
>
    <x-page-header
        :kicker="__('site.nav.membership')"
        :title="__('membership.register.heading')"
        :sub="__('membership.register.sub')"
    />

    <section class="section section-light">
        <div class="container container-narrow">
            @if ($config->isOpen())
                <form method="POST" action="{{ route('membership.register.submit') }}" class="form-card">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field form-field-full">
                            <label for="name">{{ __('membership.register.full_name') }} <span class="req">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                            @error('name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-field">
                            <label for="email">{{ __('membership.register.email') }} <span class="req">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email">
                            @error('email') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-field">
                            <label for="phone">{{ __('membership.register.phone') }}</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="tel">
                            @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-field">
                            <label for="gender">{{ __('membership.register.gender') }}</label>
                            <select id="gender" name="gender">
                                <option value="">—</option>
                                <option value="male" @selected(old('gender') === 'male')>{{ __('membership.register.gender_male') }}</option>
                                <option value="female" @selected(old('gender') === 'female')>{{ __('membership.register.gender_female') }}</option>
                                <option value="other" @selected(old('gender') === 'other')>{{ __('membership.register.gender_other') }}</option>
                            </select>
                            @error('gender') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-field">
                            <label for="date_of_birth">{{ __('membership.register.date_of_birth') }}</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-field">
                            <label for="location">{{ __('membership.register.location') }}</label>
                            <input type="text" id="location" name="location" value="{{ old('location') }}" maxlength="255">
                            @error('location') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-field form-field-full">
                            <label>{{ __('membership.payments.choose_method') }} <span class="req">*</span></label>
                            <div class="form-grid">
                                @foreach (\App\Models\Payment::METHODS as $method => $label)
                                    <label class="method-card">
                                        <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method') === $method) required>
                                        <span class="method-card-body">
                                            <strong>{{ __('membership.payments.'.$method) }}</strong>
                                            <small>{{ $config->formattedRegistrationFee() }}</small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gold">{{ __('membership.register.submit') }}</button>
                        <p class="form-note">{{ __('membership.plans.note') }} · {{ __('membership.plans.registration_fee') }}: {{ $config->formattedRegistrationFee() }}</p>
                    </div>
                </form>
            @else
                <div class="admin-panel">
                    <h2>{{ __('membership.register.closed_heading') }}</h2>
                    <p>{{ __('membership.register.closed_text') }}</p>
                    <a href="{{ route('membership') }}" class="btn btn-gold">{{ __('site.cta.back') }}</a>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
