@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        {{ $initiator->first_name . ' ' . $initiator->last_name }} hat dich soeben einem Bug zugwiesen.<br /><br />
        <strong>Projekt</strong>: {{ $project->designation }}<br />
        <strong>Bug</strong>: {{ $bug->designation }} (ID: {{ $bug->ai_id }})<br /><br />
        Um die den Bug anzeigen zu lassen, kannst Du einfach das entsprechende Projekt im Webpanel aufsuchen.<br />
        Über den folgenden Button gelangst du ins Webpanel:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Zum Dashboard
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent
