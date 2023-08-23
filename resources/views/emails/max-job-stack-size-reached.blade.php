@component('mail::message', ['locale' => 'en'])
    <p>Hello BugShot team,</p>
    <p>
        This is a notification to inform you, that the jobs table just exceeded the maximum stack size of {{ config('app.max_job_stack_size') }}.<br />
		Current count: {{ $jobCount }}.<br />
        You may want to check the server to see if any issues are present. (e.g. Daemon stopped running)
    </p>
    <p>
        Error-free Greetings,
        <br />
        your {{ config('app.projectname') }} server
    </p>
@endcomponent
