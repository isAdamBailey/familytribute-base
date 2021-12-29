@component('mail::message', ['appName' => $siteName])
# Notable Dates

This is a weekly email from **{{ $siteName }}**
letting you know there are some people from your family who have notable dates coming up this week.

You may click on their name to access their page, and share to social media!

<ul>
@foreach($birthDates as $birthDate)
<li>
<a href="{{ config('app.url') }}/{{ $birthDate->person->slug }}">
    <strong>{{ $birthDate->person->full_name }}</strong>
</a>
was born {{ \Carbon\Carbon::parse($birthDate->birth_date)->diffForHumans() }} on
<strong>{{ \Carbon\Carbon::parse($birthDate->birth_date)->toFormattedDateString() }}</strong>
</li>
@endforeach

@foreach($deathDates as $deathDate)
<li>
<a href="{{ config('app.url') }}/{{ $deathDate->person->slug }}">
    <strong>{{ $deathDate->person->full_name }}</strong>
</a>
died {{ \Carbon\Carbon::parse($deathDate->death_date)->diffForHumans() }} on
<strong>{{ \Carbon\Carbon::parse($deathDate->death_date)->toFormattedDateString() }}</strong>
</li>
@endforeach
</ul>

@component('mail::button', ['url' => config('app.url')])
Visit {{ $siteName }}
@endcomponent

@endcomponent
