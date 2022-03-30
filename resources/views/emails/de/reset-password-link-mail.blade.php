<!DOCTYPE html>
<html class="mail">
    <head>
        <style>
            html.mail {
                font-family: 'Arial';
            }

            html.mail body {
                max-width: 600px;
                border: 1px solid #C8C8C8;
                margin: 50px auto;
            }

            html.mail body header {
                background-color: #7A2EE6;
                padding: 17px 50px;
                position: relative;
                overflow: hidden;
            }

            html.mail header figure {
                overflow: hidden;
                margin: 0;
            }

            html.mail header figure img {
                overflow: hidden;
                margin: 0;
                max-width: 152px;
                min-width: 120px;
            }

            html.mail header div {
                position: absolute;
                right: 50px;
                top: 50%;
                transform: translate(0, -50%);
            }

            html.mail h1 {
                margin: 0;
                color: #ffffff;
                font-size: 18px;
            }

            html.mail body main {
                padding: 20px 50px;
            }

            html.mail .action-buttons-wrapper {
                margin-top: 50px;
            }

            html.mail main a.action-button {
                background-color: #18D992;
                font-size: 14px;
                font-weight: bold;
                border-radius: 21px;
                padding: 12px 25px;
                color: #fff;
                text-decoration: none;
                text-transform: uppercase;
                display: block;
                width: fit-content;
                margin: 30px 0px;
            }

            html.mail a {
                color: #7A2EE6;
            }

            html.mail body footer {
                margin: 0 50px;
                overflow: hidden;
                border-top: 1px solid #C8C8C8;
                padding: 25px 0;
            }

            html.mail body footer figure {
                width: calc(40% - 40px);
                min-width: 120px;
                margin: 0 40px 25px 0;
                float: left;
                overflow: hidden;
                background-color: #000;
            }

            html.mail body footer div {
                text-align: right;
                float: left;
                width: 60%;
            }

            html.mail body footer div * {
                margin-top: 0;
            }

            html.mail body footer div > a {
                text-decoration: none;
                font-weight: bold;
            }
        </style>
        <title>BugShot - Passwortzurücksetzung</title>
    </head>
    <body>
        <header>
            <figure>
                <img src="{{ asset('img/bugshot_logo_white.png') }}" alt="BugShot">
            </figure>
            <div>
                <h1>Passwortzurücksetzung</h1>
            </div>
        </header>
        <main>
            <p>Hallo {{ $user->first_name }},</p>
            <p>
                Du hast eine Passwortzurücksetzung angefordert.<br /><br />
                Verwende bitte folgenden Link um dein Passwort zurückzusetzen:
            </p>
            <a href="{{ $url }}" type="button" class="action-button">Passwort zurücksetzen</a>
            <p>
                Falls das nicht klappt, kannst Du auch einfach die folgende URL in deinen Browser kopieren:
            </p>
            <a href="{{ $url }}">{{ $url }}</a>
            <p>
                Fehlerfreie Grüße,
                <br />
                dein BugShot Team
            </p>
        </main>
        <footer>
            <figure>
                <img src="{{ asset('img/bugshot_logo.svg') }}" alt="BugShot">
            </figure>
            <div>
                <p>Indem du BugShot nutzt, stimmst du unseren <a href="{{ config('app.proposal_url') . '/terms-and-conditions' }}">Bedingungen und Konditionen</a> zu.</p>
                <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
            </div>
        </footer>
    </body>
</html>