@component('mail::message')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        Deine Passwortzurücksetzung war erfolgreich!<br /><br />
        Du kannst dich nun mit deinen neuen Zugangsdaten im Dashboard anmelden.<br />
        Klicke dazu einfach auf den folgenden Button:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Zum Dashboard
    @endcomponent
    <p>
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    </p>
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent