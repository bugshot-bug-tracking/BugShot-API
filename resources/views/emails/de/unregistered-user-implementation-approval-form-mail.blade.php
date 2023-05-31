@component('mail::message', ['locale' => $locale])
    <p>Hallo,</p>
    <p>
        Du hast soeben ein Umsetzungsfreigabe Formular erhalten.<br />
		In diesem kannst du die Umsetzung der gelisteten Tasks ablehnen oder freigeben und deine Entscheidung direkt an <br />deinen technischen Partner weiterleiten.<br /><br />
        Klicke auf den folgenden Button, um zum Formular zu gelangen:
    </p>
    @component('mail::button', ['url' => $url])
        Zur Umsetzungsfreigabe
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ $url }}">{{ $url }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent
