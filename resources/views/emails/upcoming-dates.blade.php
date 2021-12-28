@component('mail::message')
# Weekly Notable Dates

This is a weekly email from **{{ $siteName }}**
letting you know there are some people from your family who have notable dates coming up this week.

You may click on their name to access their page, and share to social media!

@foreach($birthDates as $birthDate)
<a href="{{ $birthDate->person->slug }}">{{ $birthDate->person->full_name }}</a> was born on
{{ \Carbon\Carbon::parse($birthDate->birth_date)->toFormattedDateString() }} <br>
@endforeach

@foreach($deathDates as $deathDate)
<a href="{{ $deathDate->person->slug }}">{{ $deathDate->person->full_name }}</a> died on
{{ \Carbon\Carbon::parse($deathDate->death_date)->toFormattedDateString() }} <br>
@endforeach

@component('mail::button', ['url' => config('app.url')])
Visit {{ $siteName }}
@endcomponent

@endcomponent
