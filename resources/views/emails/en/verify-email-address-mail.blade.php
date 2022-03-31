@extends('emails.layout')

@section('header')
    <div>
        <h1>Verifizierung</h1>
    </div>
@endsection

@section('main')
    <p>Hello {{ $user->first_name }},</p>
    <p>
        vielen Dank für deine Anmeldung. Es ist soweit: Bald hast Du wieder mehr Zeit für die schönen Dinge im Leben!
        Um BugShot und damit unser volles Potential zu nutzen, musst Du nur noch deine E-Mail-Adresse verifizieren.
        <br /><br />
        In order to do that, just click on the following button:
    </p>
    <a href="{{ $url }}" type="button" class="action-button">Jetzt verifizieren</a>
    <p>
        If that doesn't work, you can also just copy the following URL into your browser:
    </p>
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a>
    <p>
        Error-free Greetings,
        <br />
        your BugShot team
    </p>
@endsection

@section('footer')
    <div>
        <p>By using BugShot, you are agreeing to our <a href="{{ config('app.proposal_url') . '/terms-and-conditions' }}">terms and conditions</a>.</p>
        <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
    </div>
@endsection
