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
        <title>BugShot - Verification</title>
    </head>
    <body>
        <header>
            <figure>
                <img src="{{ asset('img/bugshot_logo_white.png') }}" alt="BugShot">
            </figure>
            <div>
                <h1>Verification</h1>
            </div>
        </header>
        <main>
            <p>Hello {{ $user->first_name }},</p>
            <p>
                thank you for your verification!<br /><br />
                You are now a member of the BugShot family. Together we make the digital world bug-free!<br /><br />
                Best you first look around a bit. In order to do that, just click on the following button:
            </p>
            <a href="{{ config('app.webpanel_url') }}" type="button" class="action-button">Go to Dashboard</a>
            <p>
                If that doesn't work, you can also just copy the following URL into your browser:
            </p>
            <a href="{{ config('app.webpanel_url') }}">{{ config('app.webpanel_url') }}</a>
            <p>
                Error-free Greetings,
                <br />
                your BugShot team
            </p>
        </main>
        <footer>
            <figure>
                <img src="{{ asset('img/bugshot_logo.svg') }}" alt="BugShot">
            </figure>
            <div>
                <p>By using BugShot, you are agreeing to our <a href="{{ config('app.proposal_url') . '/terms-and-conditions' }}">terms and conditions</a>.</p>
                <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
            </div>
        </footer>
    </body>
</html>