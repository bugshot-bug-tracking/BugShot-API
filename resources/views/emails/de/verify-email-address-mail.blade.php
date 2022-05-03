@extends('emails.layout')

@section('header')
    <div>
        <h1>Verifizierung</h1>
    </div>
@endsection

@section('main')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        vielen Dank für deine Anmeldung! 
        <br /><br />
        Es ist soweit: Bald hast Du wieder mehr Zeit für die schönen Dinge im Leben!
        Um BugShot und damit unser volles Potential zu nutzen, musst Du nur noch deine E-Mail-Adresse verifizieren.
        <br /><br />
        Klick dazu einfach auf den folgenden Button:
    </p>
    <a href="{{ $url }}" type="button" class="action-button">Jetzt verifizieren</a>
    <p>
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    </p>
    <a href="{{ $url }}">{{ $url }}</a>
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