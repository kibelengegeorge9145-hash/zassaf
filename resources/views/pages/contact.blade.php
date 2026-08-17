@php
    $channels = [
        ['label' => __('contact.whatsapp'), 'icon' => 'whatsapp', 'url' => setting('whatsapp_url'), 'external' => true],
        ['label' => __('contact.email'), 'icon' => 'mail', 'url' => setting('email') ? 'mailto:'.setting('email') : null, 'external' => false],
        ['label' => __('contact.instagram'), 'icon' => 'instagram', 'url' => setting('instagram_url'), 'external' => true],
        ['label' => __('contact.facebook'), 'icon' => 'facebook', 'url' => setting('facebook_url'), 'external' => true],
        ['label' => __('contact.tiktok'), 'icon' => 'tiktok', 'url' => setting('tiktok_url'), 'external' => true],
        ['label' => __('contact.telegram'), 'icon' => 'telegram', 'url' => setting('telegram_url'), 'external' => true],
    ];
@endphp

<x-layouts.public
    :title="__('seo.contact.title')"
    :description="__('seo.contact.desc')"
>
    <x-page-header
        :kicker="__('site.nav.contact')"
        :title="__('contact.heading')"
        :sub="__('contact.sub')"
    />

    <section class="section section-light">
        <div class="container">
            <div class="contact-grid">
                <div>
                    <x-section-heading :title="__('contact.reach_heading')" :sub="__('contact.reach_text')" align="left" />

                    <div class="contact-channels">
                        @foreach ($channels as $channel)
                            @if ($channel['url'])
                                <a href="{{ $channel['url'] }}" class="contact-channel"
                                   @if ($channel['external']) target="_blank" rel="noopener noreferrer" @endif>
                                    <span class="contact-channel-icon"><x-icon :name="$channel['icon']" /></span>
                                    <span>
                                        <strong>{{ $channel['label'] }}</strong>
                                        <small>{{ str_replace(['https://', 'http://', 'mailto:'], '', $channel['url']) }}</small>
                                    </span>
                                </a>
                            @endif
                        @endforeach

                        @if (setting('contact_phone'))
                            <a href="tel:{{ setting('contact_phone') }}" class="contact-channel">
                                <span class="contact-channel-icon"><x-icon name="phone" /></span>
                                <span>
                                    <strong>{{ __('contact.phone') }}</strong>
                                    <small>{{ setting('contact_phone') }}</small>
                                </span>
                            </a>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="form-card">
                        <h3>{{ __('contact.form_heading') }}</h3>
                        <p class="form-card-sub">{{ __('contact.form_sub') }}</p>

                        <form method="POST" action="{{ route('contact.store') }}">
                            @csrf
                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="name">{{ __('forms.labels.name') }} <span class="req">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="120" placeholder="{{ __('forms.placeholders.full_name') }}">
                                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field">
                                    <label for="email">{{ __('forms.labels.email') }} <span class="req">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="190" placeholder="{{ __('forms.placeholders.email') }}">
                                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field">
                                    <label for="phone">{{ __('forms.labels.phone') }}</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" maxlength="30" placeholder="{{ __('forms.placeholders.phone') }}">
                                    @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field">
                                    <label for="subject">{{ __('forms.labels.subject') }}</label>
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" maxlength="190" placeholder="{{ __('forms.placeholders.subject') }}">
                                    @error('subject') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field form-field-full">
                                    <label for="message">{{ __('forms.labels.message') }} <span class="req">*</span></label>
                                    <textarea id="message" name="message" rows="5" required maxlength="5000" placeholder="{{ __('forms.placeholders.message') }}">{{ old('message') }}</textarea>
                                    @error('message') <span class="field-error">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-gold">{{ __('site.cta.send') }}</button>
                                <p class="form-note">{{ __('forms.note') }}</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
