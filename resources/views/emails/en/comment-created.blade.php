@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        {{ $commentCreator->first_name . ' ' . $commentCreator->last_name }} has just created a comment in one of the bugs you created.<br /><br />
        <strong>Project</strong>: {{ $project->designation }}<br />
        <strong>Bug</strong>: {{ $bug->ai_id }} ({{ $bug->designation }})<br />
        <strong>Comment</strong>: {{ $readableContent }}<br /><br />
        To reply to the comment you can simply visit the corresponding bug in the webpanel.<br />
        Use the following button to enter the webpanel:
    </p>
    @component('mail::button', ['url' => config('app.webpanel_url')])
        Go to Dashboard
    @endcomponent
    @component('mail::paragraph')
        If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
