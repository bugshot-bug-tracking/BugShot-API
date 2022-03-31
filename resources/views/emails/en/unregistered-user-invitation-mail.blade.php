@extends('emails.layout')

@section('header')
    <div>
        <h1>Invitation</h1>
    </div>
@endsection

@section('main')
    <p>Hello {{ $user->first_name }},</p>
    <p>
        {{ $entryMessage }}<br /><br />
        To accept the invitation you have to register at BugShot with this email address.<br />
        In order to do that, just click on the following button:
    </p>
    <a href="{{ config('app.webpanel_url') . '/auth/register' }}" type="button" class="action-button">Register now</a>
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