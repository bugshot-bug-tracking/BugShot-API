@component('mail::message', ['locale' => $locale])
    <p>Hallo,</p>
    <p>
        {{ $sender . " " . $entryMessage }}<br /><br />
        Um die Einladung anzunehmen musst du dich mit dieser E-Mail Adresse bei BugShot registrieren.<br />
        Klick dazu einfach auf den folgenden Button:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url') . '/auth/register'])
        Jetzt registrieren
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ config('app.webpanel_url') . '/auth/register' }}">{{ config('app.webpanel_url') . '/auth/register' }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent
