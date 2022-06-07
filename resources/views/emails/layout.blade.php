<!DOCTYPE html>
<html lang="en" class="mail">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width">
        <title>BugShot - @yield('title')</title>
        <style></style>
    </head>

    <body>
        <div id="email">

            <!-- Header --> 
            <table role="presentation" border="0" cellspacing="0" width="100%" class="header">
                <tr>
                    <td>
                        <figure>
                            <img src="{{ asset('img/bugshot_logo_white.png') }}" alt="BugShot">
                        </figure>
                    </td>
                    <td>
                        @yield('header')
                    </td>
                </tr>
            </table>

            <!-- Main --> 
            <table role="presentation" border="0" cellspacing="0" width="100%" class="main">
                <tr>
                    <td>
                        @yield('main')
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <table role="presentation" border="0" cellspacing="0" width="100%" class="footer">
                <tr>
                    <td class="logo-td">
                        <figure>
                            <img src="{{ asset('img/bugshot_logo.png') }}" alt="BugShot">
                        </figure>
                    </td>
                    <td>
                        @yield('footer')
                    </td>
                </tr>
            </table>

        </div>
    </body>