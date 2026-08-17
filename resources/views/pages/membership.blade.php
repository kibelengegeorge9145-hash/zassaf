<x-layouts.public
    :title="__('seo.membership.title')"
    :description="__('seo.membership.desc')"
>
    <x-page-header
        :kicker="__('site.nav.membership')"
        :title="__('community.membership_heading')"
        :sub="__('community.membership_text')"
    />

    {{-- STATUS BANNER --}}
    <section class="section section-light">
        <div class="container">
            <div class="admin-panel">
                @if ($config->isOpen())
                    <div class="membership-status-banner">
                        <span class="chip chip-gold">{{ __('membership.status_open') }}</span>
                        <div>
                            <strong>{{ __('community.membership_heading') }}</strong>
                            <p>{{ __('membership.launch_date', ['date' => $config->launchDate()->format('d M Y')]) }}</p>
                        </div>
                        <a href="{{ route('membership.register') }}" class="btn btn-gold">{{ __('site.cta.join_community') }}</a>
                    </div>
                @else
                    <div class="membership-status-banner">
                        <span class="chip">{{ __('membership.coming_soon') }}</span>
                        <div>
                            <strong>{{ __('community.membership_heading') }}</strong>
                            <p>{{ __('membership.launch_date', ['date' => $config->launchDate()->format('d M Y')]) }}</p>
                        </div>
                        @if (auth()->check() && auth()->user()->isMember())
                            <a href="{{ route('member.dashboard') }}" class="btn btn-gold">{{ __('membership.nav.dashboard') }}</a>
                        @else
                            <a href="{{ route('member.login') }}" class="btn btn-ghost">{{ __('membership.login.heading') }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- BENEFITS --}}
    <section class="section section-black">
        <div class="container">
            <x-section-heading
                :kicker="__('site.nav.membership')"
                :title="__('membership.benefits.heading')"
                :align="'center'"
            />
            <div class="card open-card">
                <ul class="check-list check-list-lg">
                    @foreach (__('membership.benefits.points') as $point)
                        <li><x-icon name="check" /> {{ $point }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- FEES --}}
    <section class="section section-light">
        <div class="container">
            <x-section-heading
                :kicker="__('community.membership_plans_note')"
                :title="__('membership.plans.note')"
                :align="'center'"
            />
            <div class="membership-plans membership-plans-lg">
                <div class="membership-plan-card">
                    <span class="chip chip-gold">{{ __('membership.plans.registration_fee') }}</span>
                    <strong class="plan-price">{{ $config->formattedRegistrationFee() }}</strong>
                    <span class="plan-cycle">{{ __('community.registration_fee') }}</span>
                </div>
                <div class="membership-plan-card">
                    <span class="chip chip-gold">{{ __('membership.plans.monthly_fee') }}</span>
                    <strong class="plan-price">{{ $config->formattedMonthlyFee() }}</strong>
                    <span class="plan-cycle">{{ __('membership.plans.per_month') }}</span>
                </div>
            </div>

            @if (! $config->isOpen())
                <div class="register-form-wrap">
                    <h3>{{ __('membership.notify_heading') }}</h3>
                    <p>{{ __('membership.notify_text') }}</p>
                    <form method="POST" action="{{ route('register.interest') }}" class="form-card">
                        @csrf
                        <input type="hidden" name="type" value="membership">
                        <input type="hidden" name="reference" value="membership">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="full_name">{{ __('forms.labels.full_name') }} <span class="req">*</span></label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required maxlength="120" autocomplete="name">
                                @error('full_name') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-field">
                                <label for="email">{{ __('forms.labels.email') }} <span class="req">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email">
                                @error('email') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-gold">{{ __('membership.notify_me') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="register-form-wrap">
                    <a href="{{ route('membership.register') }}" class="btn btn-gold btn-lg">{{ __('membership.register.heading') }}</a>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
