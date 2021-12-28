<!DOCTYPE html>
<html>
<head>
    <title>BugShot - Your Password Reset Link</title>
</head>
<body>
    <h1>Reset Password:</h1>
    <p>Hi {{ $user->first_name }}! Please follow the follwing link to reset your password:</p>
    <div class="action-buttons-wrapper">
        <a href="{{ $url }}" type="button">Reset Password</a>
    </div>
</body>
</html>