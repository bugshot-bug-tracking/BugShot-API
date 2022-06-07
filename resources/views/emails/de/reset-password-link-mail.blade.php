@component('mail::message')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        Du hast eine Passwortzurücksetzung angefordert.<br /><br />
        Verwende bitte folgenden Link um dein Passwort zurückzusetzen:
    </p>
    @component('mail::button', ['url' => $url])
        Passwort zurücksetzen
    @endcomponent
    <p>
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    </p>
    <a href="{{ $url }}">{{ $url }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent