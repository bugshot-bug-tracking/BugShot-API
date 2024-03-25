@component('mail::message', ['locale' => $locale])
    <p>Hallo {{ $user->first_name }},</p>
    <p>
        es gibt Neuigkeiten in einem der Projekte in dem du mitwirkst.<br /><br />
		<strong>Die letzten Aktivitäten im Projekt "{{ $project->designation }}" ({{ $groupsWording ? Str::singular($groupsWording) : "Gruppe" }}: {{  $project->company->designation }})</strong><br />
		Neue Tasks: {{ count($bugs) }}<br />
		Neue Kommentare: {{ count($comments) }}<br />
		Abgeschlossene Tasks: {{ count($doneBugs) }}<br /><br />
		Untenstehend findest du eine detaillierte Aufstellung der Aktivitäten:<br /><br />
		<table>
			<tr>
				<td>
					<strong>Neue Tasks</strong><br />
					@foreach ($bugs as $bug)
						@component('mail::listitem', ['url' => config('app.webpanel_url'), 'id' => $bug->ai_id])
							{{ $bug->designation }}<br />
						@endcomponent
					@endforeach
				</td>
			</tr>
		</table><br />
		<table>
			<tr>
				<td>
					<strong>Neue Kommentare</strong><br />
					@foreach ($comments as $comment)
						@component('mail::listitem', ['url' => config('app.webpanel_url'), 'id' => $comment->bug->ai_id])
							{{ $comment->bug->designation }}<br />
							{{ $comment->user->first_name . " " . $comment->user->last_name }} schrieb:<br />
							{{ $comment->content }}<br />
						@endcomponent
					@endforeach<br /><br />
				</td>
			</tr>
		</table><br />
		<table>
			<tr>
				<td>
					<strong>Erledigte Tasks</strong><br />
					@foreach ($doneBugs as $doneBug)
						@component('mail::listitem', ['url' => config('app.webpanel_url'), 'id' => $doneBug->ai_id])
							{{ $doneBug->designation }}<br />
						@endcomponent
					@endforeach<br /><br />
				</td>
			</tr>
		</table><br /><br />
    </p>
    @component('mail::button', ['url' => $projectBaseUrl])
        Zum Dashboard
    @endcomponent
    @component('mail::paragraph')
        Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
    @endcomponent
    <a href="{{ $projectBaseUrl }}">{{ $projectBaseUrl }}</a><br /><br />
    <p>
        Fehlerfreie Grüße,
        <br />
        dein {{ config('app.projectname') }} Team
    </p>
@endcomponent
