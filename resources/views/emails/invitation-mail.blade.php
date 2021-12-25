<!DOCTYPE html>
<html>
<head>
    <title>BugShot - Invitation received</title>
</head>
<body>
    <h1>Invitation:</h1>
    <p>Hi {{ $user->first_name }}! {{ $entryMessage }}. Feel free to accept/decline the invitation by clicking on the respective buttons:</p>
    <div class="action-buttons-wrapper">
        <a href="{{ route('user.invitation.accept', ['invitation' => $invitation->id]) }}" type="button">Accept</a>
        <a href="{{ route('user.invitation.decline', ['invitation' => $invitation->id]) }}" type="button">Decline</a>
    </div>
</body>
</html>