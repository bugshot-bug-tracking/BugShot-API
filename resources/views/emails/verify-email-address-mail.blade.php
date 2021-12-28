<!DOCTYPE html>
<html>
<head>
    <title>BugShot - Verify E-Mail Address</title>
</head>
<body>
    <h1>Verify E-Mail Address:</h1>
    <p>Hi {{ $user->first_name }}! You just made your life a whole lot easier by rocking with BugShot. Please verify your email address via clicking on the following button</p>
    <div class="action-buttons-wrapper">
        <a href="{{ $url }}" type="button">Verify email address</a>
    </div>
</body>
</html>