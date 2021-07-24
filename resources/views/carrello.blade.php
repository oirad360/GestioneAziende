@extends('layouts.page')

@section('title','Carrello')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionCarrello.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/containerProdotti.css'>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/carrello.js' defer></script>
    @parent
@endsection

@section('section')
            <section data-tipo="carrello">
                <h1 id='descrizione'>Carrello di {{$username}}</h1>
                <main class="containerProdotti">

                </main>
                <p id="totale">
                </p>
                <button id='ordina' @if($countCarrello>0) class="hidden" @endif>
                    Ordina
                </button>
            </section>
@endsection
