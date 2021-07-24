
<!DOCTYPE html>
<html>
    <head>
        @section('style')
            <link rel='stylesheet' href='/{{$app_folder}}/public/style/footerHeader.css'>
            <link rel='stylesheet' href='/{{$app_folder}}/public/style/modal.css'>
        @show
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Raleway&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300&family=Roboto:wght@300&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Oxygen&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <script src='/{{$app_folder}}/public/scripts/env.js' defer></script>
        @section('scripts')
            <script src='/{{$app_folder}}/public/scripts/modal.js' defer></script>
            <script src='/{{$app_folder}}/public/scripts/sideNav.js' defer></script>
        @show
        <title>Gestione Aziende - @yield('title')</title>
    </head>
    <body>

            <header>
                <nav>
                    <img id="searchButton" src="/{{$app_folder}}/public/assets/search.png"/>
                    <a href='/{{$app_folder}}/public/home'>Home</a>
                    <a href='/{{$app_folder}}/public/info'>Info</a>
                    <a href='/{{$app_folder}}/public/logout'>Logout</a>
                </nav>
                <h1 id="titolo">GESTIONE AZIENDE</h1>
                <div class="profile">
                    <div class="propic" style="background-image:url({{$propicURL}});">
                    </div>
                    <a href="/{{$app_folder}}/public/profilo">
                        <p>Benvenuto {{$username}}</p>
                    </a>
                </div>
                <a href='/{{$app_folder}}/public/carrello' id='carrello'>
                    <div id='countCarrello'>
                        {{$countCarrello}}
                    </div>
                </a>
                <div class='overlay'></div>
            </header>
            <div class="hidden" id="sideNav">
                <div id="firstSeparator"class="separator">
                    <div id="group">
                        <div>CERCA PRODOTTI</div>
                        <img id="mostraProdotti"src="/{{$app_folder}}/public/assets/triangolo.png"/>
                    </div>
                    <div id="close">X</div>
                </div>
                <div id="prodottiSide" class="hidden">
                    <input type="text"/>
                    <form name="formSide" method='POST'>
                        <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                        <label>Categoria: <select name="categoria">
                                <option value="Qualsiasi">Qualsiasi</option>
                            @foreach($categorieSide as $categoriaSide)
                                <option value={{$categoriaSide["categoria"]}}>{{$categoriaSide["categoria"]}}</option>
                            @endforeach
                        </select></label>
                        <label>Ordina: <select name="ordine">
                            <option value="titolo">A-Z</option>
                            <option value="titolo desc">Z-A</option>
                            <option value="prezzo">Prezzo crescente</option>
                            <option value="prezzo desc">Prezzo decrescente</option>
                        </select></label>
                    </form>
                    <div class="containerProdotti">
                    </div>
                </div>
                <div class="separator">
                    <div>CERCA UTENTI</div>
                    <img id="mostraUtenti"src="/{{$app_folder}}/public/assets/triangolo.png"/>
                </div>
                <div id="utentiSide" class="hidden">
                    <input type="text"/>
                    <div class="containerProdotti">
                    </div>
                </div>
            </div>

            @yield('section')

            <footer>

                <a href="https://www.unict.it">
                    <img id="logoUNICT" src="/{{$app_folder}}/public/assets/logoUNICT.jpg">
                </a>
                <p>
                    Dario Anzalone <br>
                    O46002090 <br>
                    <em>Web Programming 2021 <br>
                    DIEEI - Cittadella Universitaria</em>
                </p>
                
            </footer>
            <div id="modal" class="hidden"></div>
    </body>
</html>