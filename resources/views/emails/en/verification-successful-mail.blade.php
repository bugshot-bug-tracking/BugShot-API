@extends('emails.layout')

@section('header')
    <div>
        <h1>Verification</h1>
    </div>
@endsection

@section('main')
    <p>Hello {{ $user->first_name }},</p>
    <p>
        thank you for your verification!<br /><br />
        You are now a member of the BugShot family. Together we make the digital world bug-free!<br /><br />
        Best you first look around a bit. In order to do that, just click on the following button:
    </p>
    <a href="{{ config('app.webpanel_url') }}" type="button" class="action-button">Go to Dashboard</a>
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
