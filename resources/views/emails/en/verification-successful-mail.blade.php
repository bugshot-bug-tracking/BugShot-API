@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        Thank you for your verification!<br /><br />
        You are now a member of the {{ config('app.projectname') }} family. Together we improve the bug-fixing process in
        the digital world.
        <br /><br />
        Best you first look around a bit.
        This button takes you to the dashboard:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Go to Dashboard
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a>
    <br /><br />
    <p>
        Here you can find all browser extensions and more: <a href="{{ config('app.webpanel_url') . '/user/settings' }}">{{ config('app.webpanel_url') . '/user/settings' }}</a>
		<br /><br />
        You can find helpful documents here: <a href="https://www.bugshot.de/hilfreiche-dokumente">https://www.bugshot.de/hilfreiche-dokumente</a>
		<br /><br />
        Do you have questions, feedback, requests or found a bug? Write to us at hello@bugshot.de.
    </p>
    <br /><br />
    <p>
        Bugfree greetings,
        <br />
        The {{ config('app.projectname') }} team
    </p>
@endcomponent
