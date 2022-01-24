<!DOCTYPE html>
<html>
<head>
    <title>BugShot - Invitation received</title>
</head>
<body>
    <h1>Invitation:</h1>
    <p>Hi {{ $user->first_name }}! {{ $entryMessage }}. Feel free to accept/decline the invitation within your dashboard:</p>
    <div class="action-buttons-wrapper">
        <a href="{{ config('app.webpanel_url') }}" type="button">Go to Dashboard</a>
    </div>
</body>
</html>