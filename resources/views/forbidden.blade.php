@extends('layouts.page')

@section('title','')

@section('style')
    @parent
    <link rel='stylesheet' href='/{{$app_folder}}/public/style/sectionInfo.css'>
@endsection

@section('scripts')
    @parent
@endsection

@section('section')
            <section>
            <h1>L'accesso a questa pagina è riservato agli impiegati</h1>
            </section>
@endsection
