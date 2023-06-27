@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        es gibt Neuigkeiten in einem der Projekte in dem du mitwirkst.<br /><br />
		<strong>Die letzten Aktivitäten im Projekt {{ $project->designation }}</strong><br />
		Neue Tasks: {{ count($bugs) }}<br />
		Neue Kommentare: {{ count($comments) }}<br />
		Abgeschlossene Tasks: {{ count($doneBugs) }}<br /><br />
		Untenstehend findest du eine detaillierte Aufstellung der Aktivitäten:<br /><br />
		<strong>Neue Tasks</strong><br />
		@foreach ($bugs as $bug)
			@component('mail::button', ['url' => config('app.webpanel_url')])
				#{{ $bug->ai_id }}
			@endcomponent
			{{ $bug->designation }}<br />
		@endforeach<br /><br />
		<strong>Neue Kommentare</strong><br />
		@foreach ($comments as $comment)
			@component('mail::button', ['url' => config('app.webpanel_url')])
				#{{ $comment->bug->ai_id }}
			@endcomponent
			{{ $comment->bug->designation }}<br />
			{{ $comment->user->first_name . " " . $comment->user->last_name }} schrieb:<br />
			{{ $comment->content }}<br />
		@endforeach<br /><br />
		<strong>Erledigte Tasks</strong><br />
		@foreach ($doneBugs as $doneBug)
			@component('mail::button', ['url' => config('app.webpanel_url')])
				#{{ $doneBug->ai_id }}
			@endcomponent
			{{ $doneBug->designation }}<br />
		@endforeach<br /><br />
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
