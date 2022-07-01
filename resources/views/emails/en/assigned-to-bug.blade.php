@component('mail::message')
    <p>Hello {{ $user->first_name }},</p>
    <p>
        {{ $initiator->first_name . ' ' . $initiator->last_name }} has just assigned you to a bug.<br /><br />
        <strong>Project</strong>: {{ $project->designation }}<br />
        <strong>Bug</strong>: {{ $bug->designation }} (ID: {{ $bug->ai_id }})<br /><br />
        To view the bug, you can simply go to the corresponding project in the webpanel.<br />
        The following button will take you to the webpanel:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Go to Dashboard
    @endcomponent
    <p>
        If that doesn't work, you can also just copy the following URL into your browser:
    </p>
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent