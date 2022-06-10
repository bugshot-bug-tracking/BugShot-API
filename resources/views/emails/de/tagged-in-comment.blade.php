@component('mail::message')
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        {{ $commentCreator->first_name . ' ' . $commentCreator->last_name }} hat dich soeben in einem Kommentar erwähnt.<br /><br />
        Projekt: {{ $project->designation }}<br />
        Bug: {{ $bug->ai_id }} ({{ $bug->designation }})<br />
        Kommentar: {{ $readableContent }}<br /><br />
        Um auf den Kommentar zu antworten kannst Du einfach den entsprechenden Bug im Webpanel aufsuchen.<br />
        Über den folgenden Button gelangst du ins Webpanel:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Zum Dashboard
    @endcomponent
    <p>
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    </p>
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent