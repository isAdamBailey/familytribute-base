@component('mail::message')
{{ __('You have been invited to join  :appname!', ['appname' => $appName]) }}

{{ __('If you do not have an account, you may create one by clicking the button below.') }}

{{ __('Please use the following registration secret: :secret', ['secret' => $secret]) }}

@component('mail::button', ['url' => route('register')])
{{ __('Create Account') }}
@endcomponent

{{ __('If you did not expect to receive an invitation to '.$appName.', you may discard this email.') }}
@endcomponent
