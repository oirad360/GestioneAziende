@extends('layouts.page')

@section('title','Recensioni')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/containerProdotti.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionRecensioni.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/recensioni.css'>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/recensioni.js' defer></script>
    @parent
@endsection

@section('section')
    <section data-tipo="recensioni" data-product='{{$prodotto}}'>
        <h1 id="titoloRecensioni">Recensioni per {{$prodotto}}</h1>
        <div class="containerProdotti">
        </div>
        <p id="votoMedio"></p>
        <div id="recensioni">
        </div>
        <div @if(isset($miaRecensione)) class="hidden" @endif>
            <button id="bottoneRecensione">Scrivi una recensione</button>
            <div id="areaRecensione" class="hidden">
                <textarea name="testoRecensione" form="formRecensione" maxlength=255></textarea>
                <form name="formRecensione" id="formRecensione">
                    <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                    <label>Voto: <select name="voto">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select></label>
                    <input type='submit' value="Pubblica recensione"/>
                </form>
                <span class="error hidden">Inserisci il testo</span>
            </div>
        </div>
    </section>
@endsection

