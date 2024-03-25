@component('mail::message', ['locale' => $locale])
    <p>Hello {{ $user->first_name }},</p>
    <p>
        There is news in one of the projects you are involved in.<br /><br />
		<strong>The latest activities in the project "{{ $project->designation }}" ({{ $groupsWording ? Str::singular($groupsWording) : "Group" }}: {{  $project->company->designation }})</strong><br />
		New tasks: {{ count($bugs) }}<br />
		New comments: {{ count($comments) }}<br />
		Completed tasks: {{ count($doneBugs) }}<br /><br />
		Below you will find a detailed list of activities:<br /><br />
		<table>
			<tr>
				<td>
					<strong>New tasks</strong><br />
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
					<strong>New comments</strong><br />
					@foreach ($comments as $comment)
						@component('mail::listitem', ['url' => config('app.webpanel_url'), 'id' => $comment->bug->ai_id])
							{{ $comment->bug->designation }}<br />
							{{ $comment->user->first_name . " " . $comment->user->last_name }} wrote:<br />
							{{ $comment->content }}<br />
						@endcomponent
					@endforeach<br /><br />
				</td>
			</tr>
		</table><br />
		<table>
			<tr>
				<td>
					<strong>Completed tasks</strong><br />
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
        Go to Dashboard
    @endcomponent
    @component('mail::paragraph')
	If that doesn't work, you can also just copy the following URL into your browser:
    @endcomponent
    <a href="{{ $projectBaseUrl }}">{{ $projectBaseUrl }}</a><br /><br />
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
