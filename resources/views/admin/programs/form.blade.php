@php
    $editing = isset($program) && $program->exists;
    $icons = ['sparkles', 'rocket', 'briefcase', 'lightbulb', 'users', 'mic', 'book-open', 'target', 'eye', 'shield', 'globe'];
@endphp

<x-layouts.admin :title="$editing ? __('admin.programs.edit') : __('admin.programs.create')">
    <div class="admin-section">
        <h2>{{ $editing ? __('admin.programs.edit') : __('admin.programs.create') }}</h2>
    </div>

    <form method="POST" action="{{ $editing ? route('admin.programs.update', $program) : route('admin.programs.store') }}" class="form-card admin-form">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-field">
                <label for="title_en">{{ __('admin.programs.fields.title_en') }} <span class="req">*</span></label>
                <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $program->title_en ?? '') }}" required maxlength="255">
                @error('title_en') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="title_sw">{{ __('admin.programs.fields.title_sw') }}</label>
                <input type="text" id="title_sw" name="title_sw" value="{{ old('title_sw', $program->title_sw ?? '') }}" maxlength="255">
                @error('title_sw') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field form-field-full">
                <label for="description_en">{{ __('admin.programs.fields.description_en') }} <span class="req">*</span></label>
                <textarea id="description_en" name="description_en" rows="4" required maxlength="2000">{{ old('description_en', $program->description_en ?? '') }}</textarea>
                @error('description_en') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field form-field-full">
                <label for="description_sw">{{ __('admin.programs.fields.description_sw') }}</label>
                <textarea id="description_sw" name="description_sw" rows="4" maxlength="2000">{{ old('description_sw', $program->description_sw ?? '') }}</textarea>
                @error('description_sw') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label for="icon">{{ __('admin.programs.fields.icon') }}</label>
                <select id="icon" name="icon">
                    @foreach ($icons as $icon)
                        <option value="{{ $icon }}" @selected(old('icon', $program->icon ?? 'sparkles') === $icon)>{{ $icon }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-field">
                <label for="sort_order">{{ __('admin.programs.fields.sort_order') }}</label>
                <input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $program->sort_order ?? 0) }}">
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $program->is_published ?? true))>
                    <span>{{ __('admin.programs.fields.is_published') }}</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-ghost">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
</x-layouts.admin>
