@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        thank you for your registration!
        <br /><br />
        The time has come: Soon you will have more time for the good things in life!
        To use {{ config('app.projectname') }} and thus our full potential, you only need to verify your email address.
        <br /><br />
        In order to do that, just click on the following button:
    </p>
    @component('mail::button', ['url' => $url])
        Verify now
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ $url }}" class="plain-link">{{ $url }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
