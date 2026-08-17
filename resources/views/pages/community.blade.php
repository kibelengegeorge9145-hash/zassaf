<x-layouts.public
    :title="__('seo.community.title')"
    :description="__('seo.community.desc')"
>
    <x-page-header
        :kicker="__('site.nav.community')"
        :title="__('community.heading')"
        :sub="__('site.tagline')"
    />

    {{-- OPEN & FREE --}}
    <section class="section section-light">
        <div class="container">
            <div class="two-col two-col-center">
                <div>
                    <p class="kicker">{{ __('site.nav.community') }}</p>
                    <h2>{{ __('community.open_heading') }}</h2>
                    <p>{{ __('community.open_text') }}</p>
                    <a href="#register" class="btn btn-gold">{{ __('site.cta.join_community') }}</a>
                </div>
                <div class="card open-card">
                    <ul class="check-list check-list-lg">
                        @foreach (__('community.open_points') as $point)
                            <li><x-icon name="check" /> {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- MEMBERSHIP --}}
    <section class="section section-black">
        <div class="container">
            <div class="membership-band">
                <div class="membership-band-intro">
                    <p class="kicker">{{ __('community.membership_heading') }}</p>
                    <h2 class="section-title-inverse">{{ __('community.membership_heading') }}</h2>
                    <p class="section-sub-inverse">{{ __('community.membership_text') }}</p>
                    <p class="section-sub-inverse">{{ __('community.membership_plans_note') }}</p>
                </div>

                <div class="membership-plans membership-plans-lg">
                    @foreach ($plans as $plan)
                        <div class="membership-plan-card">
                            <span class="chip chip-gold">{{ $plan->name }}</span>
                            <p>{{ $plan->description }}</p>
                            <strong class="plan-price">{{ $plan->formatted_price }}</strong>
                            @if ($plan->billing_cycle === 'monthly')
                                <span class="plan-cycle">{{ __('community.monthly') }}</span>
                            @else
                                <span class="plan-cycle">{{ __('community.registration_fee') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- REGISTRATION --}}
    <x-registration-form
        :programs="$programs"
        :events="$events"
        :include-membership="true"
    />
</x-layouts.public>
