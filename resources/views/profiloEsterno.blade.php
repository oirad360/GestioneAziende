@extends('layouts.page')

@section('title','Profilo')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionProfilo.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/containerProdotti.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/recensioni.css'>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/profiloEsterno.js' defer></script>
    @parent
@endsection

@section('section')
<section data-username='{{ $usernameProfilo }}'>
                <h1 id='descrizione'>Profilo di {{ $usernameProfilo }}</h1>
                <p>Nome: {{$nome}} <br> Cognome: {{$cognome}}<br> Registrato dal {{$dataRegistrazione}}</p>
                <h1 id='descrizione'>
                    @if(count($recensioni)>0)
                        Recensioni di {{$usernameProfilo}}
                    @else
                        {{$usernameProfilo}} non ha ancora scritto recensioni
                    @endif
                </h1> <!--Recensioni di $username oppure $username non ha ancora scritto recensioni-->
                <div id="recensioni">
                </div>
                @if(!isset($nascondi))
                <div id="impiegato">
                    <p>
                        Impiego attuale: <strong>{{$impiego}}</strong> dal <span id="dataAssunzione">{{$dataAssunzione}}</span>
                    </p>
                    
                    <button id="mostraImpieghi">Mostra impieghi passati</button>
                    <div id="impieghiPassati" class="hidden">
                        @if(count($impieghiPassati)>0)
                            @foreach($impieghiPassati as $impiegoPassato)
                                <div><strong>{{$impiegoPassato["nome"]}}</strong>: dal {{$impiegoPassato["dataAssunzione"]}} al {{$impiegoPassato["fineImpiego"]}}</div>
                            @endforeach
                        @else
                            Nessun risultato trovato.
                        @endif
                    </div>
                <div>
                @endif
            </section>
@endsection

