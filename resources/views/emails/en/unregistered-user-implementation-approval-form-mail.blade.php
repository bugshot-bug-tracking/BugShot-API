@component('mail::message', ['locale' => $locale])
    <p>Hello,</p>
    <p>
        You have just received an implementation approval form.<br />
		In it, you can reject or approve the implementation of the listed tasks and forward your<br />decision directly to your technical partner.<br /><br />
        Click on the following button to get to the form:
    </p>
    @component('mail::button', ['url' => $url])
        Go to form
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ $url }}">{{ $url }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent

