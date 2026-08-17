@php $editing = isset($convo) && $convo->exists; @endphp

<x-layouts.admin :title="$editing ? __('admin.convos.edit') : __('admin.convos.create')">
    <div class="admin-section">
        <h2>{{ $editing ? __('admin.convos.edit') : __('admin.convos.create') }}</h2>
    </div>

    <form method="POST" action="{{ $editing ? route('admin.convos.update', $convo) : route('admin.convos.store') }}" class="form-card admin-form">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-field">
                <label for="title_en">{{ __('admin.convos.fields.title_en') }} <span class="req">*</span></label>
                <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $convo->title_en ?? '') }}" required maxlength="255">
                @error('title_en') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="title_sw">{{ __('admin.convos.fields.title_sw') }}</label>
                <input type="text" id="title_sw" name="title_sw" value="{{ old('title_sw', $convo->title_sw ?? '') }}" maxlength="255">
            </div>

            <div class="form-field form-field-full">
                <label for="description_en">{{ __('admin.convos.fields.description_en') }} <span class="req">*</span></label>
                <textarea id="description_en" name="description_en" rows="4" required maxlength="3000">{{ old('description_en', $convo->description_en ?? '') }}</textarea>
            </div>

            <div class="form-field form-field-full">
                <label for="description_sw">{{ __('admin.convos.fields.description_sw') }}</label>
                <textarea id="description_sw" name="description_sw" rows="4" maxlength="3000">{{ old('description_sw', $convo->description_sw ?? '') }}</textarea>
            </div>

            <div class="form-field form-field-full">
                <label for="topics_en">{{ __('admin.convos.fields.topics_en') }}</label>
                <textarea id="topics_en" name="topics_en" rows="3" maxlength="2000">{{ old('topics_en', $convo->topics_en ?? '') }}</textarea>
            </div>

            <div class="form-field form-field-full">
                <label for="topics_sw">{{ __('admin.convos.fields.topics_sw') }}</label>
                <textarea id="topics_sw" name="topics_sw" rows="3" maxlength="2000">{{ old('topics_sw', $convo->topics_sw ?? '') }}</textarea>
            </div>

            <div class="form-field">
                <label for="event_date">{{ __('admin.convos.fields.event_date') }}</label>
                <input type="date" id="event_date" name="event_date" value="{{ old('event_date', $convo->event_date?->format('Y-m-d') ?? '') }}">
            </div>

            <div class="form-field">
                <label for="event_time">{{ __('admin.convos.fields.event_time') }}</label>
                <input type="text" id="event_time" name="event_time" value="{{ old('event_time', $convo->event_time ?? '') }}" maxlength="100" placeholder="e.g. 10:00 AM - 12:00 PM">
            </div>

            <div class="form-field">
                <label for="platform_en">{{ __('admin.convos.fields.platform_en') }}</label>
                <input type="text" id="platform_en" name="platform_en" value="{{ old('platform_en', $convo->platform_en ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label for="platform_sw">{{ __('admin.convos.fields.platform_sw') }}</label>
                <input type="text" id="platform_sw" name="platform_sw" value="{{ old('platform_sw', $convo->platform_sw ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label for="speaker_en">{{ __('admin.convos.fields.speaker_en') }}</label>
                <input type="text" id="speaker_en" name="speaker_en" value="{{ old('speaker_en', $convo->speaker_en ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label for="speaker_sw">{{ __('admin.convos.fields.speaker_sw') }}</label>
                <input type="text" id="speaker_sw" name="speaker_sw" value="{{ old('speaker_sw', $convo->speaker_sw ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $convo->is_published ?? true))>
                    <span>{{ __('admin.convos.fields.is_published') }}</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
            <a href="{{ route('admin.convos.index') }}" class="btn btn-ghost">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
</x-layouts.admin>
