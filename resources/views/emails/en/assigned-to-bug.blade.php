@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        {{ $initiator->first_name . ' ' . $initiator->last_name }} has just assigned you to a bug.<br /><br />
        <strong>Project</strong>: {{ $project->designation }}<br />
        <strong>Bug</strong>: {{ $bug->designation }} (ID: {{ $bug->ai_id }})<br /><br />
        To view the bug, you can simply go to the corresponding project in the webpanel.<br />
        The following button will take you to the webpanel:
    </p>
    @component('mail::button', ['url' => $projectBaseUrl . "?b=" . $bug->id])
        Go to Dashboard
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ $projectBaseUrl . "?b=" . $bug->id }}">{{ $projectBaseUrl . "?b=" . $bug->id }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
