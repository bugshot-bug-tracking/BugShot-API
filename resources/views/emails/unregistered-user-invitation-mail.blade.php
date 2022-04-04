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
        <title>BugShot - Einladung erhalten</title>
    </head>
    <body>
        <h1>Invitation:</h1>
        <p>Hallo {{name}},<br />
        <br />
        du wurdest von Daniel Michel zu dem Projekt Zeitraum eingeladen.<br />
        Um die Einladung anzunehmen musst Du dich auf BugShot mit dieser E-Mail-Adresse registrieren.<br />
        Mit einem Klick auf den unten stehenden Button kommst Du direkt zur Registrierung.<br />
        <br />
        Button [JETZT REGISTRIEREN]<br />
<br />
Falls das nicht klappt, kannst Du auch einfach die folgende URL https://app.bugshot.de/register in deinen Browser kopieren.<br />
<br />
Fehlerfreie Grüße,<br />
dein BugShot Team<br />
<br />

        {{ $entryMessage }}</p>
        <div class="action-buttons-wrapper">
            <a href="{{ $registerUrl }}" type="button">Register</a>
        </div>
    </body>
</html>