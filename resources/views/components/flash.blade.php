
@if (session('success'))
    <div class="alert alert-success" role="alert">
        <x-icon name="check" />
        <span>{{ session('success') }}</span>
        <button type="button" class="alert-close" data-dismiss-alert aria-label="Close">×</button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <div class="alert-head">
            <strong>{{ __('site.messages.error') }}</strong>
        </div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="alert-close" data-dismiss-alert aria-label="Close">×</button>
    </div>
@endif
