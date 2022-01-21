@component('mail::message', ['appName' => $appName])
{{ __('You have been invited to join :appname!', ['appname' => $appName]) }}

With an account at {{ $appName }}, you have access to exclusive stories and pictures
not available on the public website. You may also receive emails with reminders of notable dates for your family.

{{ __('You may create an account by clicking the button below.') }}

{{ __('Please use the following secret phrase when you register: :secret', ['secret' => $secret]) }}

@component('mail::button', ['url' => route('register')])
{{ __('Create Account') }}
@endcomponent

{{ __('If you did not expect to receive an invitation to '.$appName.', you may discard this email.') }}
@endcomponent
