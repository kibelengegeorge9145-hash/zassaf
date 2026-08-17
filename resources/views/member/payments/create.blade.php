<x-layouts.member :title="__('membership.payments.choose_method')">
    <div class="admin-section">
        <h2>{{ __('membership.payments.choose_method') }}</h2>
        <p class="admin-sub">{{ __('membership.payments.method_hint') }}</p>
    </div>

    @php
        $type = request('type') === 'registration' ? 'registration' : 'monthly';
        $amount = $type === 'registration'
            ? \App\Support\MembershipConfig::formattedRegistrationFee()
            : \App\Support\MembershipConfig::formattedMonthlyFee();
    @endphp

    <form method="POST" action="{{ route('member.payments.store') }}" class="form-card admin-form">
        @csrf

        <input type="hidden" name="type" value="{{ $type }}">

        <div class="form-grid">
            @foreach (\App\Models\Payment::METHODS as $method => $label)
                <label class="method-card">
                    <input type="radio" name="payment_method" value="{{ $method }}" required>
                    <span class="method-card-body">
                        <strong>{{ __('membership.payments.'.$method) }}</strong>
                        <small>{{ $amount }}</small>
                    </span>
                </label>
            @endforeach
        </div>

        @error('payment_method') <span class="field-error">{{ $message }}</span> @enderror

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('membership.payments.start') }}</button>
            <a href="{{ route('member.dashboard') }}" class="btn btn-ghost">{{ __('membership.payments.cancel') }}</a>
        </div>
    </form>
</x-layouts.member>
