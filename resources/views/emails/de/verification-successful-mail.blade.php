@component('mail::message')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        vielen Dank für deine Verifizierung!<br /><br />
        Du bist jetzt Mitglied der BugShot-Familie. Zusammen machen wir die digitale Welt fehlerfrei!<br /><br />
        Am besten du schaust dich erstmal etwas um. Klick dazu einfach auf den folgenden Button:
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