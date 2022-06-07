@component('mail::message')
    <p>Hello {{ $user->first_name }},</p>
    <p>
        Your password reset was successful!<br /><br />
        You can now log in to the dashboard with your new credentials.<br />
        In order to do that, just click on the following button:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Go to Dashboard
    @endcomponent
    <p>
        If that doesn't work, you can also just copy the following URL into your browser:
    </p>
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
