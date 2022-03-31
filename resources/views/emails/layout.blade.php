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
                width: calc(40% - 40px);
                min-width: 120px;
                margin: 0 40px 0 0;
                float: left;
                overflow: hidden;
            }

            html.mail figure img {
                overflow: hidden;
                margin: 0;
                max-width: 152px;
                min-width: 120px;
            }

            html.mail header div {
                text-align: right;
                float: left;
                width: 60%;
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

            @media only screen and (max-width: 600px) {
                html.mail body header figure, html.mail body footer figure {
                    margin: 0 auto 20px;
                    float: none;
                    width: 80%;
                    text-align: center;
                }

                html.mail header figure img {
                    width: 100%;
                }

                html.mail header div {
                    text-align: center;
                    width: 100%;
                }

                html.mail header div h1 {
                    font-size: 16px;
                }

                html.mail body footer div {
                    width: 100%;
                    text-align: center;
                }
            }
        </style>
        <title>BugShot - @yield('title')</title>
    </head>
    <body>
        <header>
            <figure>
                <img src="{{ asset('img/bugshot_logo_white.png') }}" alt="BugShot">
            </figure>
            @yield('header')
        </header>
        <main>
            @yield('main')
        </main>
        <footer>
            <figure>
                <img src="{{ asset('img/bugshot_logo.png') }}" alt="BugShot">
            </figure>
            @yield('footer')
        </footer>
    </body>
</html>