@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        {{ $commentCreator->first_name . ' ' . $commentCreator->last_name }} hat soeben einen Kommentar zu einem von dir erstellten Bug hinzugefügt.<br /><br />
        <strong>{{ $groupsWording ? Str::singular($groupsWording) : "Gruppe" }}</strong>: {{ $project->company->designation }}<br />
		<strong>Projekt</strong>: {{ $project->designation }}<br />
        <strong>Bug</strong>: {{ $bug->designation }} (ID: {{ $bug->ai_id }})<br />
        <strong>Kommentar</strong>: {{ $readableContent }}<br /><br />
        Um auf den Kommentar zu antworten kannst Du einfach den entsprechenden Bug im Webpanel aufsuchen.<br />
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
