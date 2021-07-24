@extends('layouts.page')

@section('title','Info')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionInfo.css'>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/info.js' defer></script>
    @parent
@endsection

@section('section')
            <section>
                <h1>Mostra impiegati</h1>
                <form name="mostraImpiegati" method='POST'>
                <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                            <select name="aziende">
                                @foreach($elementiForm as $elementoForm)
                                    <option value={{$elementoForm["id"]}}>{{$elementoForm["nome"]}}</option>
                                @endforeach
                            </select>
                            <input nome="submit" type='submit' value="Conferma"/>
                </form>
                <div id="impiegati">
                </div>
                <h1>Prezzi delle azioni</h1>
                <form name="azioni" method='POST'>
                    <input type='hidden' name='_token' value='{{ $csrf_token }}'>
                    <label>Azienda: 
                        <select name="database_dataset">
                            <option value='WIKI/MSFT'>Microsoft</option>
                            <option value='WIKI/AAPL'>Apple</option>
                            <option value='HKEX/01810'>Xiaomi</option>
                        </select>
                    </label>
                    <label>Data di inizio: <input type='date' name='start_date'></label>
                    <label>Data di fine: <input type='date' name='end_date'></label>
                    <label>Cadenza: 
                        <select name='collapse'>
                            <option value='daily'>Giornaliero</option>
                            <option value='weekly'>Settimanale</option>
                            <option value='monthly'>Mensile</option>
                            <option value='quarterly'>Trimestrale</option>
                            <option value='annual'>Annuale</option>
                        </select>
                    </label>
                    <label><input type='submit' value="Crea tabella"></label>
                </form>
                <p id='text' ></p>
                <div id='table'>
                    <table>
                    
                    </table>
                </div>
            </section>
@endsection