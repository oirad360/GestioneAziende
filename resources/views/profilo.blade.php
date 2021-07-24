@extends('layouts.page')

@section('title','Profilo')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionProfilo.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/containerProdotti.css'>
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/recensioni.css'>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/profilo.js' defer></script>
    @parent
@endsection

@section('section')
<section data-tipo="profilo">
                <h1 id='descrizione'>Profilo di {{$username}}</h1>
                <p>Nome: {{$nome}} <br> Cognome: {{$cognome}}<br> Registrato dal {{$dataRegistrazione}}</p>
                <h1 id='titoloWishlist'></h1>
                <div id="wishlist" class='containerProdotti'>
                </div>

                <h1 id='titoloAcquisti'></h1>
                <div id="acquisti" class="containerProdotti">
                </div>
                <h1 id='titoloRecensioni'></h1>
                <div id="recensioni">
                </div>
                <button id="cambiaImmagine">Cambia immagine del profilo</button>
                <form class="hidden" name="propic" enctype="multipart/form-data">
                    <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                    <input name="image" type="file" accept='.jpg, .jpeg, image/gif, image/png'/>
                    <input name="submit" type="submit" value="Conferma"/>
                    <div id="errorContainer">
                        <span class="error hidden">Inserisci un formato valido (jpeg, jpg, gif o png).</span>
                        <span class="error hidden">La dimensione dell'immagine non può superare i 2MB.</span>
                        <span id="compila" class="hidden">Inserisci un'immagine.</span>
                    </div>
                </form>
                @if(!isset($nascondi))
                <div id="impiegato">
                    <p>Stipendio: {{$stipendio}}</p>
                    <p>
                        Impiego attuale: <strong>{{$impiego}}</strong> dal <span id="dataAssunzione">{{$dataAssunzione}}</span>
                    </p>
                    <button id="cambiaImpiego">Cambia impiego</button>
                    <form class="hidden" name="cambiaImpiego">
                            <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                            <select name="impiego">
                                @foreach($elementiForm as $elementoForm)
                                    <option value={{$elementoForm["id"]}}>{{$elementoForm["nome"]}}</option>
                                @endforeach
                            </select>
                            <input nome="submit" type='submit' value="Conferma"/>
                    </form>

                    <button id="mostraImpieghi">Mostra impieghi passati</button>
                    <div id="impieghiPassati"class="hidden">
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

