@component('mail::message', ['locale' => $locale])
	<p>Hello,</p>
	<p>
		You have just received an implementation release.<br />
		In it you can see which tasks your customer has approved or rejected for implementation.<br /><br />
		You can find the corresponding PDF in the attachment.
	</p>
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} team
    </p>
@endcomponent
