@php
    $assetPath = '/images/undraw_appreciation_re_p6rl.svg';
@endphp

<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">

@if( file_exists(public_path($assetPath)) )
<img src="{{ $url.$assetPath }}" class="logo" alt="{{ $appName ?? config('app.name') }} Logo">
@endif

<h1>{{ $slot }}</h1>

</a>
</td>
</tr>
