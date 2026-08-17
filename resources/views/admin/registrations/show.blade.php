<x-layouts.admin :title="__('admin.registrations.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ $registration->full_name }}</h2>
            <p class="admin-sub">{{ __('admin.registrations.show_sub') }}</p>
        </div>
        <a href="{{ route('admin.registrations.index') }}" class="btn btn-ghost">{{ __('admin.actions.back') }}</a>
    </div>

    <div class="admin-detail-grid">
        <div class="admin-panel">
            <h3>{{ __('admin.details') }}</h3>
            <dl class="detail-list">
                <div><dt>{{ __('forms.labels.full_name') }}</dt><dd>{{ $registration->full_name }}</dd></div>
                <div><dt>{{ __('forms.labels.email') }}</dt><dd><a href="mailto:{{ $registration->email }}">{{ $registration->email }}</a></dd></div>
                <div><dt>{{ __('forms.labels.phone') }}</dt><dd>{{ $registration->phone ?? '—' }}</dd></div>
                <div><dt>{{ __('admin.registrations.type') }}</dt><dd>{{ $registration->type_label }}</dd></div>
                <div><dt>{{ __('admin.reference') }}</dt><dd>{{ $registration->reference ?? '—' }}</dd></div>
                <div><dt>{{ __('admin.registrations.submitted') }}</dt><dd>{{ $registration->created_at->format('d M Y H:i') }}</dd></div>
                <div><dt>{{ __('admin.registrations.status') }}</dt><dd><span class="chip chip-gold">{{ $registration->status_label }}</span></dd></div>
            </dl>

            @if ($registration->message)
                <h3>{{ __('forms.labels.message') }}</h3>
                <p class="admin-message">{{ $registration->message }}</p>
            @endif
        </div>

        <div class="admin-panel">
            <h3>{{ __('admin.actions.toggle_status') }}</h3>
            <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">
                @csrf
                @method('PATCH')
                <div class="form-field">
                    <select name="status" required>
                        @foreach (\App\Models\Registration::STATUSES as $status)
                            <option value="{{ $status }}" @selected($registration->status === $status)>{{ __('admin.status_' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
