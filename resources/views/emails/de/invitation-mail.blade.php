<!DOCTYPE html>
<html class="mail" id="invitation-received-mail">
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
        <header></header>
        <main>
            <p>Hello ###USER###,</p>
            <p>
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
                At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
            </p>
            <div class="action-buttons-wrapper">
                <a href="{{ config('app.webpanel_url') }}" type="button">Go to Dashboard</a>
            </div>
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
                <img src="{{ asset('img/bugshot-logo.jpg') }}" alt="BugShot">
            </figure>
            <div>
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>
                <a href="{{ config('app.proposal_url') }}">{{ config('app.proposal_url') }}</a>
            </div>
        </footer>
    </body>
</html>