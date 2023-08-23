@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        vielen Dank für deine Verifizierung!
        <br /><br />
        Du bist jetzt Mitglied der BugShot-Familie. Zusammen verbessern wir Bugfixing-Prozesse in der digitalen Welt.
        <br /><br />
        Am besten du schaust dich erstmal etwas um.
        Über diesen Button gelangst du zum Dashboard:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        ZUM DASHBOARD
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a>
    <br /><br />
    <p>
        Hier findest du alle Browser-Extensions und mehr:  <a href="{{ config('app.webpanel_url') . '/user/settings' }}">{{ config('app.webpanel_url') . '/user/settings' }}</a>
        <br /><br />
        Hier findest du hilfreiche Dokumente: <a href="https://www.bugshot.de/hilfreiche-dokumente">https://www.bugshot.de/hilfreiche-dokumente</a>
        <br /><br />
        Du hast Fragen, Feedback, Wünsche oder einen Bug gefunden? Schreib uns gerne an hello@bugshot.de.
    </p>
    <br /><br />
    <p>
        Bugfreie Grüße schickt dir,
        <br />
        Das {{ config('app.projectname') }} Team
    </p>
@endcomponent
