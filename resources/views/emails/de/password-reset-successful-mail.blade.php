@extends('emails.layout')

@section('header')
    <div>
        <h1>Passwortzurücksetzung</h1>
    </div>
@endsection

@section('main')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        Deine Passwortzurücksetzung war erfolgreich!<br /><br />
        Du kannst dich nun mit deinen neuen Zugangsdaten im Dashboard anmelden.<br />
        Klicke dazu einfach auf den folgenden Button:
    </p>
    <a href="{{ config('app.proposal_url') }}" type="button" class="action-button">Zum Dashboard</a>
    <p>
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    </p>
    <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
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