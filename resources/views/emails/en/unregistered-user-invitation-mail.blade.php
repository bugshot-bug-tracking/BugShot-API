@component('mail::message', ['locale' => $locale])
    <p>Hello there,</p>
    <p>
        {{ $sender . " " . $entryMessage }}<br /><br />
        To accept the invitation you have to register at {{ config('app.projectname') }} with this email address.<br />
        In order to do that, just click on the following button:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url') . '/auth/register?email=' . base64_encode($notifiable->routes['email'])])
        Register now
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ config('app.webpanel_url') . '/auth/register' }}">{{ config('app.webpanel_url') . '/auth/register?email=' . base64_encode($notifiable->routes['email']) }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
