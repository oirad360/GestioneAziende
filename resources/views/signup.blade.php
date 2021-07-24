@extends('layouts.form')

@section('scripts')
@parent
<script src='/{{$app_folder}}/public/scripts/signup.js' defer></script>
@endsection

@section('title','Login')

@section('descrizione', 'Registrati inserendo i tuoi dati')

@section('form')
<form name="form" method='POST' enctype="multipart/form-data">
                <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                <label class="radio"><input type="radio" name="tipo" value="cliente" {{ $checkCliente }}  />Cliente</label>
                <label class="radio"><input type="radio" name="tipo" value="impiegato" {{ $checkImpiegato }} />Impiegato</label>
                <div id="nome">
                    <label>Nome <input name="nome" type="text" @if(isset($old_name)) value="{{ $old_name }}" @endif></label>
                    <span class="error hidden">Questo campo è obbligatorio</span>
                    <span class="error hidden">Il nome può contenere solo lettere</span>
                </div>
                <div id="cognome">
                    <label>Cognome <input name="cognome" type="text" value='@if(isset($old_surname)) {{ $old_surname }} @endif'></label>
                    <span class="error hidden">Questo campo è obbligatorio</span>
                    <span class="error hidden">Il cognome può contenere solo lettere</span>
                </div>
                <div id="username">
                    <label>Username <input name="username" type="text" value='@if(isset($old_username)) {{ $old_username }} @endif'></label>
                    <span class="error hidden">Questo campo è obbligatorio</span>
                    <span class="error hidden">L'username può contenere solo caratteri alfanumerici</span>
                    <span class="error hidden">Username già in uso</span>
                </div>
                <div id="email">
                    <label>Email <input name="email" type="text" value='@if(isset($old_email)) {{ $old_email }} @endif'></label>
                    <span class="error hidden">Questo campo è obbligatorio</span>
                    <span class="error hidden">Email non valida</span>
                    <span class="error hidden">Email già in uso</span>
                </div>
                <div id="password">
                    <label>Password <input name="password" type="password"></label>
                    <span class="error hidden">Questo campo è obbligatorio</span>
                    <span class="error hidden">La password deve contenere almeno 8 caratteri</span>
                    <span class="error hidden">La password deve contenere almeno una lettera</span>
                    <span class="error hidden">La password deve contenere almeno un numero</span>
                </div>
                <div id="confermaPass">
                    <label>Conferma password <input name="confermaPass" type="password"></label>
                    <span class="error hidden">Le password non coincidono</span>
                </div>
                <div id="image">
                    <label>Immagine di profilo <input name="image" type="file" accept='.jpg, .jpeg, image/gif, image/png'/></label>
                    <span class="error hidden">L'immagine non può avere dimensioni superiori a 2MB</span>
                    <span class="error hidden">Inserisci un'immagine (png, jpg, gif)</span>
                </div>
                <label class="hidden impiegato">Impiego 
                    <select name="impiego">
                         @foreach($impieghi as $impiego)
                            <option value={{ $impiego["value"] }}> {{$impiego["nome"]}} </option>
                         @endforeach
                    </select>
                </label>
                <div id="assunzione">
                    <label class="hidden impiegato">Data di assunzione <input name="dataAssunzione" type="date" @if(isset($old_assunzione)) value={{ $old_assunzione }} @endif></label>
                    <span class="error hidden">Inserisci una data passata</span>
                </div>
                <div id="stipendio">
                    <label class="hidden impiegato">Stipendio <input name="stipendio" type="text" value='@if(isset($old_stipendio)) {{ $old_stipendio }} @endif'></label>
                    <span class="error hidden">Questo campo è obbligatorio</span>
                    <span class="error hidden">Questo campo può contenere solo numeri</span>
                </div>
                <span id="compila" class="error hidden">Compila tutti i campi</span>
                <label><input type="submit"/></label>
                <p id="errorphp">
                    @if(isset($errors))
                        @foreach($errors as $error)
                            {{ $error }}
                        @endforeach
                    @endif
                </p>
                <p>Sei già registrato? <a href="/{{$app_folder}}/public/login">Effettua l'accesso</a></p>
            </form>
@endsection