<html class="mail">
    <head>
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