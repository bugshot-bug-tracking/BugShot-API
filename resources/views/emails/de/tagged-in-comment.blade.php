@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        {{ $commentCreator->first_name . ' ' . $commentCreator->last_name }} hat dich soeben in einem Kommentar erwähnt.<br /><br />
		<strong><a href="{{ $groupBaseUrl }}">{{ $groupsWording ? Str::singular($groupsWording) : "Gruppe" }}</a></strong>: {{ $company->designation }}<br />
        <strong><a href="{{ $projectBaseUrl }}">Projekt</a></strong>: {{ $project->designation }}<br />
        <strong><a href="{{ $projectBaseUrl . "?b=" . $bug->id }}">Bug</a></strong>: {{ $bug->designation }} (ID: {{ $bug->ai_id }})<br />
        <strong><a href="{{ $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id }}">Kommentar</a></strong>: {{ $readableContent }}<br /><br />
        Um auf den Kommentar zu antworten kannst Du einfach den entsprechenden Bug im Webpanel aufsuchen.<br />
        Über den folgenden Button gelangst du ins Webpanel:
    </p>
    @component('mail::button', ['url' => $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id])
        Zum Dashboard
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id }}">{{ $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent
