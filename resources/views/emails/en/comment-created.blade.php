@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        {{ $commentCreator->first_name . ' ' . $commentCreator->last_name }} has just created a comment in one of the bugs you created.<br /><br />
		<strong><a href="{{ $groupBaseUrl }}">{{ $groupsWording ? Str::singular($groupsWording) : "Group" }}</a></strong>: {{ $company->designation }}<br />
		<strong><a href="{{ $projectBaseUrl }}">Project</a></strong>: {{ $project->designation }}<br />
        <strong><a href="{{ $projectBaseUrl . "?b=" . $bug->id }}">Bug</a></strong>: {{ $bug->ai_id }} ({{ $bug->designation }})<br />
        <strong><a href="{{ $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id }}">Comment</a></strong>: {{ $readableContent }}<br /><br />
        To reply to the comment you can simply visit the corresponding bug in the webpanel.<br />
        Use the following button to enter the webpanel:
    </p>
    @component('mail::button', ['url' => $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id])
        Go to Dashboard
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id }}">{{ $projectBaseUrl . "?b=" . $bug->id . "&c=" . $comment->id }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
