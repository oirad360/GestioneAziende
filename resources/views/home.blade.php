@extends('layouts.page')

@section('title','Home')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionHome.css'>
@endsection

@section('scripts')
    <script src='/{{$app_folder}}/public/scripts/home.js' defer></script>
    @parent
@endsection

@section('section')
    <section>
        <div class='descrizione'>Sezione Prodotti</div>
        <p>Scegli una categoria:</p>
        <div class="container">
        
        </div>
    </section>
@endsection

