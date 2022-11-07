@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        Thank you for your verification!<br /><br />
        You are now a member of the {{ config('app.projectname') }} family. Together we make the digital world bug-free!<br /><br />
        Best you first look around a bit. In order to do that, just click on the following button:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url') ])
        Go to Dashboard
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
