@component('mail::message', ['locale' => $locale])
    <p>
        Ein neuer Benutzer hat sich registriert!
    </p>
    <p>
        {{ $user->first_name }}
    </p>
    <p>
        {{ $user->last_name }}
    </p>
    <p>
        {{ $user->email }}
    </p>
    <p>
        {{ $user->created_at }}
    </p>
@endcomponent
