@php $editing = isset($event) && $event->exists; @endphp

<x-layouts.admin :title="$editing ? __('admin.events.edit') : __('admin.events.create')">
    <div class="admin-section">
        <h2>{{ $editing ? __('admin.events.edit') : __('admin.events.create') }}</h2>
    </div>

    <form method="POST" action="{{ $editing ? route('admin.events.update', $event) : route('admin.events.store') }}" class="form-card admin-form">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-field">
                <label for="title_en">{{ __('admin.events.fields.title_en') }} <span class="req">*</span></label>
                <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $event->title_en ?? '') }}" required maxlength="255">
            </div>

            <div class="form-field">
                <label for="title_sw">{{ __('admin.events.fields.title_sw') }}</label>
                <input type="text" id="title_sw" name="title_sw" value="{{ old('title_sw', $event->title_sw ?? '') }}" maxlength="255">
            </div>

            <div class="form-field form-field-full">
                <label for="description_en">{{ __('admin.events.fields.description_en') }} <span class="req">*</span></label>
                <textarea id="description_en" name="description_en" rows="4" required maxlength="3000">{{ old('description_en', $event->description_en ?? '') }}</textarea>
            </div>

            <div class="form-field form-field-full">
                <label for="description_sw">{{ __('admin.events.fields.description_sw') }}</label>
                <textarea id="description_sw" name="description_sw" rows="4" maxlength="3000">{{ old('description_sw', $event->description_sw ?? '') }}</textarea>
            </div>

            <div class="form-field">
                <label for="event_date">{{ __('admin.events.fields.event_date') }} <span class="req">*</span></label>
                <input type="date" id="event_date" name="event_date" value="{{ old('event_date', $event->event_date?->format('Y-m-d') ?? '') }}" required>
                @error('event_date') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="event_time">{{ __('admin.events.fields.event_time') }}</label>
                <input type="text" id="event_time" name="event_time" value="{{ old('event_time', $event->event_time ?? '') }}" maxlength="100" placeholder="e.g. 10:00 AM - 12:00 PM">
            </div>

            <div class="form-field">
                <label for="location_en">{{ __('admin.events.fields.location_en') }}</label>
                <input type="text" id="location_en" name="location_en" value="{{ old('location_en', $event->location_en ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label for="location_sw">{{ __('admin.events.fields.location_sw') }}</label>
                <input type="text" id="location_sw" name="location_sw" value="{{ old('location_sw', $event->location_sw ?? '') }}" maxlength="255">
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $event->is_published ?? true))>
                    <span>{{ __('admin.events.fields.is_published') }}</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
</x-layouts.admin>
