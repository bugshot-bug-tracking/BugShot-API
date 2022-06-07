@component('mail::message')
    <p>Hello {{ $user->first_name }},</p>
    <p>
        You have requested a password reset.<br /><br />
        Please use the following link to reset your password:
    </p>
    @component('mail::button', ['url' => $url])
        Reset Password
    @endcomponent
    <p>
        If that doesn't work, you can also just copy the following URL into your browser:
    </p>
    <a href="{{ $url }}">{{ $url }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
