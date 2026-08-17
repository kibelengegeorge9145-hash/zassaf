@props([
    'programs' => [],
    'events' => [],
    'convos' => [],
    'includeMembership' => true,
    'heading' => null,
    'text' => null,
])

<section class="section section-register" id="register">
    <div class="container container-narrow">
        <x-section-heading
            :title="$heading ?? __('home.join_section_heading')"
            :sub="$text ?? __('home.join_section_text')"
        />

        <form method="POST" action="{{ route('register.interest') }}" class="form-card">
            @csrf
            <div class="form-grid">
                <div class="form-field">
                    <label for="full_name">{{ __('forms.labels.full_name') }} <span class="req">*</span></label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required maxlength="120" autocomplete="name" placeholder="{{ __('forms.placeholders.full_name') }}">
                    @error('full_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="email">{{ __('forms.labels.email') }} <span class="req">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" placeholder="{{ __('forms.placeholders.email') }}">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="phone">{{ __('forms.labels.phone') }}</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" maxlength="30" autocomplete="tel" placeholder="{{ __('forms.placeholders.phone') }}">
                    @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="type">{{ __('forms.labels.type') }} <span class="req">*</span></label>
                    <select id="type" name="type" required data-reference-field="reference">
                        <option value="">{{ __('forms.options.choose') }}</option>

                        @foreach ($programs as $program)
                            <option value="program" data-reference="program:{{ $program->id }}" @selected(old('type') === 'program')>{{ $program->title }}</option>
                        @endforeach

                        @foreach ($events as $event)
                            <option value="event" data-reference="event:{{ $event->id }}" @selected(old('type') === 'event')>{{ $event->title }}</option>
                        @endforeach

                        @foreach ($convos as $convo)
                            <option value="weekend_convo" data-reference="convo:{{ $convo->id }}" @selected(old('type') === 'weekend_convo')>{{ $convo->title }}</option>
                        @endforeach

                        @if ($includeMembership)
                            <option value="membership" data-reference="membership" @selected(old('type') === 'membership')>{{ __('forms.options.membership') }}</option>
                        @endif

                        <option value="program" data-reference="general">{{ __('forms.options.general') }}</option>
                    </select>
                    <input type="hidden" name="reference" id="reference" value="{{ old('reference') }}">
                    @error('type') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field form-field-full">
                    <label for="message">{{ __('forms.labels.message') }}</label>
                    <textarea id="message" name="message" rows="3" maxlength="5000" placeholder="{{ __('forms.placeholders.message') }}">{{ old('message') }}</textarea>
                    @error('message') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">{{ __('site.cta.submit') }}</button>
                <p class="form-note">{{ __('forms.note') }}</p>
            </div>
        </form>
    </div>
</section>
