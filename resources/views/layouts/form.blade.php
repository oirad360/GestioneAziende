
<!DOCTYPE html>
<html>
    <head>
        <link rel='stylesheet' href='/{{$app_folder}}/public/style/form.css'>
        @section('scripts')
        <script src='/{{$app_folder}}/public/scripts/env.js' defer></script>
        @show
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Oxygen&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Raleway&display=swap" rel="stylesheet">
        <script>
            if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/{{$app_folder}}/sw.js')
            .then(function(registration) {
                console.log('Registration successful, scope is:', registration.scope);
            })
            .catch(function(error) {
                console.log('Service worker registration failed, error:', error);
            });
            }
        </script>
        <link rel="manifest" href="/{{$app_folder}}/public/manifest.json">
        <title>Gestione Aziende - @yield('title')</title>
    </head>
    <body>
    <div id="overlay"></div>
        <section>
            <h1 id="titolo">GESTIONE AZIENDE</h1>
            <p>@yield('descrizione')</p>
        </section>
        <main>
            @yield('form')
            
        </main>
        
    </body>
</html>