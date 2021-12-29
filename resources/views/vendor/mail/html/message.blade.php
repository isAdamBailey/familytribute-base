@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', [
    'url' => config('app.url'),
    'appName' => $appName ?? config('app.name')
])
{{ $appName ?? config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ $appName ?? config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
