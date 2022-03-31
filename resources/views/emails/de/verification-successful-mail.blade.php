@extends('emails.layout')

@section('header')
    <div>
        <h1>Verifizierung</h1>
    </div>
@endsection

@section('main')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        vielen Dank für deine Verifizierung!<br /><br />
        Du bist jetzt Mitglied der BugShot-Familie. Zusammen machen wir die digitale Welt fehlerfrei!<br /><br />
        Am besten du schaust dich erstmal etwas um. Klick dazu einfach auf den folgenden Button:
    </p>
    <a href="{{ config('app.webpanel_url') }}" type="button" class="action-button">Zum Dashboard</a>
    <p>
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    </p>
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a>
    <p>
        Fehlerfreie Grüße,
        <br />
        dein BugShot Team
    </p>
@endsection

@section('footer')
    <div>
        <p>Indem du BugShot nutzt, stimmst du unseren <a href="{{ config('app.proposal_url') . '/terms-and-conditions' }}">Bedingungen und Konditionen</a> zu.</p>
        <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
    </div>
@endsection