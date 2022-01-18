<!DOCTYPE html>
<html>
<head>
    <title>BugShot - Invitation received</title>
</head>
<body>
    <h1>Invitation:</h1>
    <p>Hi! {{ $entryMessage }}. As you are not a part of our community yet, feel free to register and accept/decline the invitation.</p>
    <div class="action-buttons-wrapper">
        <a href="{{ $registerUrl }}" type="button">Register</a>
    </div>
</body>
</html>