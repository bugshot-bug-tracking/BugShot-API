<!DOCTYPE html>
<html class="mail" id="verify-email-address-mail">
    <head>
        <style>
            html.mail {
                font-family: 'Arial';
            }

            html.mail body {
                max-width: 600px;
                border: 1px solid #C8C8C8;
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
                min-width: 152px;
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
                width: max-content;
                margin: 30px 0px;
            }
            html.mail main a {
                color: #7A2EE6;
            }
        </style>
        <title>BugShot - {{ __('email.verification-successful') }}</title>
    </head>
    <body>
        <header>
            <figure>
                <img src="{{ asset('img/bugshot_logo_white.png') }}" alt="BugShot">
            </figure>
            <div>
                <h1>Project Invitation</h1>
            </div>
        </header>
        <main>
            <p>Hello ###USER###,</p>
            <p>
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
                At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
            </p>
            <a href="{{ config('app.webpanel_url') }}" type="button" class="action-button">Go to Dashboard</a>
            <p>
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
            </p>
            <a href="{{ config('app.webpanel_url') . '/register' }}">{{ config('app.webpanel_url') . '/register' }}</a>
            <p>
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr
            </p>
        </main>
        <footer>
            <figure>
                <img src="{{ asset('img/bugshot_logo.svg') }}" alt="BugShot">
            </figure>
            <div>
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>
                <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
            </div>
        </footer>
    </body>
</html>