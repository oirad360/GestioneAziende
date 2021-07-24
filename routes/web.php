<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('home');
});

Route::get('login', 'LoginController@login');
Route::post('login', 'LoginController@checkLogin');
Route::post('login/check', 'LoginController@checkLoginJS');

Route::get('signup', 'SignupController@signup');
Route::post('signup', 'SignupController@checkSignup');
Route::get('signup/checkEmail/{email}', 'SignupController@checkEmail');
Route::get('signup/checkUsername/{username}', 'SignupController@checkUsername');

Route::get('logout', 'LoginController@logout');

Route::get('home', 'HomeController@home');
Route::get('home/fetchAziende', 'HomeController@fetchAziende');

Route::get('prodotti/{azienda}', 'ProdottiController@prodotti');
Route::post('prodotti/fetchProdotti/{azienda}', 'ProdottiController@fetchProdotti');
Route::get('prodotti/creativeCommons_api/{titoloProdotto}', 'ProdottiController@creativeCommons_api');

Route::get('aggiungiWishlist/{titolo}', 'ProdottiController@aggiungiWishlist');
Route::get('rimuoviWishlist/{titolo}', 'ProdottiController@rimuoviWishlist');
Route::get('aggiungiCarrello/{titolo}', 'CarrelloController@aggiungiCarrello');
Route::post('fetchProdottiSide/{titolo}', 'SideNavController@fetchProdottiSide');
Route::get('fetchUtenti/{username}', 'SideNavController@fetchUtenti');
Route::get('like/{id}', 'RecensioniController@like');
Route::get('dislike/{id}', 'RecensioniController@dislike');
Route::get('fetchUtentiLike/{id}', 'RecensioniController@fetchUtentiLike');

Route::get('carrello', 'CarrelloController@carrello');
Route::get('carrello/fetchCarrello', 'CarrelloController@fetchCarrello');
Route::get('carrello/rimuoviCarrello/{titolo}', 'CarrelloController@rimuoviCarrello');
Route::get('carrello/ordina', 'CarrelloController@ordina');

Route::get('profilo', 'ProfiloController@profilo');
Route::post('profilo/cambiaImmagine', 'ProfiloController@cambiaImmagine');
Route::post('profilo/cambiaImpiego', 'ProfiloController@cambiaImpiego');
Route::get('profilo/fetchAcquisti', 'ProfiloController@fetchAcquisti');
Route::get('profilo/fetchWishlist', 'ProfiloController@fetchWishlist');
Route::get('profilo/fetchRecensioniProfilo', 'ProfiloController@fetchRecensioniProfilo');
Route::get('profilo/eliminaRecensione/{id}', 'ProfiloController@eliminaRecensione');

Route::get('recensioni/{titolo}', 'RecensioniController@recensioni');
Route::get('recensioni/fetchProdottoRecensioni/{titolo}', 'RecensioniController@fetchProdottoRecensioni');
Route::get('recensioni/fetchRecensioni/{titolo}', 'RecensioniController@fetchRecensioni');
Route::post('recensioni/pubblicaRecensione/{titolo}', 'RecensioniController@pubblicaRecensione');

Route::get('profiloEsterno/{username}', 'ProfiloEsternoController@profilo');
Route::get('profiloEsterno/fetchRecensioniProfiloEsterno/{username}', 'ProfiloEsternoController@fetchRecensioniProfiloEsterno');

Route::get('info', 'InfoController@info');
Route::post('info/fetchImpiegati', 'InfoController@fetchImpiegati');
Route::post('info/quandl_api', 'InfoController@quandl_api');
Route::get('forbidden', function(){
    if(session('id')===null){
        return redirect('login');
    }
    $user=User::find(session('id'));
        if($user->propic==="defaultAvatar.jpg"){
            $propicURL="/".env('APP_FOLDER')."/public/assets/defaultAvatar.jpg";
        } else {
            $propicURL="/".env('APP_FOLDER')."/public/uploads/".$user->propic;
        }
    return view('forbidden')
    ->with('app_url', env('APP_URL'))
    ->with('app_folder', env('APP_FOLDER'))
    ->with('username',$user->username)
    ->with('propicURL',$propicURL)
    ->with('countCarrello',$user->numCarrello);
});