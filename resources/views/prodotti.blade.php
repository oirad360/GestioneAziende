@extends('layouts.page')

@section('title','Prodotti')

@section('style')
    @parent
    <link rel='stylesheet' href="/{{$app_folder}}/public/style/sectionProdotti.css">
    <link rel='stylesheet' href="/{{$app_folder}}/public/style/containerProdotti.css">
    <style>
            #layoutMenu{
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }
            #layoutMenu form{
                display: flex;
                flex-direction: column;
                align-items:flex-start;
            }
            #layoutMenu label{
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 3px;
            }
            #layoutMenu label input{
                width: 20%;
            }
            #layoutMenu label.hidden{
                display:none;
            }
            .hidden{
                display: none;
            }
        </style>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/suggerimentiProdotti.js' defer></script>
    <script src='/{{$app_folder}}/public/scripts/layoutCreator.js' defer></script>
    <script src='/{{$app_folder}}/public/scripts/prodotti.js' defer></script>
    @parent
@endsection

@section('section')
    <section data-tipo="prodotti" data-id='{{$azienda}}'>
        <h1 id='descrizione'>PRODOTTI {{$aziendaUpperCase}}</h1>
        <h1 id='titoloWishlist' class='hidden'>Wishlist</h1>
        <div id="wishlist" class="containerProdotti">
                    
        </div>
        <div id="ricerca">
            <label>Cerca<input type='text'></label>
            <form name="form" method='POST'>
                <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                <input type='submit' value="Filtra">
                <label>Categoria: <select name="categoria">
                        <option value="Qualsiasi">Qualsiasi</option>
                    @foreach($categorie as $categoria)
                        <option value={{$categoria["categoria"]}}>{{$categoria["categoria"]}}</option>
                    @endforeach
                </select></label>
                <label>Ordina: <select name="ordine">
                    <option value="titolo">A-Z</option>
                    <option value="titolo desc">Z-A</option>
                    <option value="prezzo">Prezzo crescente</option>
                    <option value="prezzo desc">Prezzo decrescente</option>
                </select></label>
            </form>
        </div>
        <main class="containerProdotti">

        </main>
    </section>
    <div id="suggerimenti" class="hidden">
        <div id="cerchio">
            <div id="container">
                <div class="linea"></div>
                <div class="linea"></div>
                <div class="linea"></div>
            </div>
        </div>
        <div id="numRisultati"></div>
        <div id="containerSuggerimenti" class="hidden"></div>
        <div id="text" class="hidden">Foto dai clienti</div>
    </div>
    
@endsection