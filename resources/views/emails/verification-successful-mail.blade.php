<!DOCTYPE html>
<html class="mail" id="verify-email-address-mail">
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
        <title>BugShot - {{ __('email.verification-successful') }}</title>
    </head>
    <body>
        <h1>{{ __('email.verification-successful') }}:</h1>
        <p>Hi {{ $user->first_name }}! Your verification process was succesful. Welcome to BugShot!</p>
        <div class="action-buttons-wrapper">
            <a href="{{ $url }}" type="button">{{ __('application.to-dashboard') }}</a>
        </div>
    </body>
</html>