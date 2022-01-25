<!DOCTYPE html>
<html class="mail" id="unregistered-user-invitation-mail">
    <head>
        <style>
            html.mail {
                font-family: 'Arial';
                text-align: center;
            }

            html.mail body {
                width: 80%;
                margin: 0 auto;
                background-color: #1a2040;
                color: #fff;
                padding: 50px 30px;
            }

            html.mail h1 {
                margin: 20px 20px 40px 20px;
            }

            html.mail .action-buttons-wrapper {
                margin-top: 50px;
            }

            html.mail .action-buttons-wrapper a {
                text-decoration: none;
                color: #1a2040;
                padding: 10px 20px;
                background-color: #18d891;
                border-radius: 12px;
                font-weight: 600;
            }
        </style>
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