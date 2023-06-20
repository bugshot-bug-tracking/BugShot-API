@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        vielen Dank für deine Anmeldung bei {{ config('app.projectname') }}!
        <br /><br />
        Es ist soweit: Bald hast Du wieder mehr Zeit für die schönen Dinge im Leben!
        Um {{ config('app.projectname') }} und damit unser volles Potential zu nutzen, musst Du nur noch deine
        E-Mail-Adresse verifizieren.
        <br /><br />
        Klick dazu einfach auf diesen Button:
    </p>
    @component('mail::button', ['url' => $url])
        JETZT VERIFIZIEREN
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ $url }}">{{ $url }}</a><br /><br />
    <p>
        Bugfreie Grüße schickt dir,
        <br />
        Das {{ config('app.projectname') }}-Team
    </p>
@endcomponent
