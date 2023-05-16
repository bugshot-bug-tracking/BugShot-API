@component('mail::message', ['locale' => $locale])
	<p>Hallo {{ $user->first_name }},</p>
	<p>
		Du hast soeben eine Umsetzungsfreigabe erhalten.<br />
		In diesem kannst du sehen, welche Tasks dein Kunde die zur Umsetzung freigegeben oder abgelehnt hat.<br /><br />
		Das entsprechende PDF findest du im Anhang.
	</p>
	<p>
		Fehlerfreie Grüße,
		<br />
		dein {{ config('app.projectname') }} Team
	</p>
@endcomponent
