<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@home');

Auth::routes();

Route::get('/reglement', function () {
    return view('reglement');
});

Route::group(['prefix' => 'seasons'], function () {
    Route::group(['middleware' => 'checkRole'], function () {
        Route::get('/create', 'SeasonsController@create');
        Route::post('/store', 'SeasonsController@store');
    });
    Route::get('/show/{id}', 'SeasonsController@show');
    Route::get('/show/{id}/results', 'SeasonsController@showResults');
    Route::get('/show', 'SeasonsController@showAll');
});

Route::group(['prefix' => 'grandPrixs'], function () {
    Route::group(['middleware' => 'checkRole'], function () {
        Route::get('{season_id}/create', 'GrandPrixController@create');
        Route::get('/updatePilotes/{id}', 'GrandPrixController@updatePilotes');
        Route::get('/updateRedirect/{id}', 'GrandPrixController@updateRedirect');
        Route::post('/update/{id}', 'GrandPrixController@update');
        Route::post('/storePilotes/{id}', 'GrandPrixController@storePilotes');
        Route::post('/store', 'GrandPrixController@store');
        Route::get('/destroy/{id}', 'GrandPrixController@destroy');
    });
    Route::get('/show/{id}', 'GrandPrixController@show');
});

Route::group(['prefix' => 'gp_pilote'], function () {
    Route::group(['middleware' => 'checkRole'], function () {
        Route::get('/create/{gp_id}', 'Gp_PiloteController@create');
        Route::post('/store', 'Gp_PiloteController@store');
    });
});

Route::group(['prefix' => 'pilotes'], function () {
    Route::group(['middleware' => 'checkRole'], function () {
        Route::get('/create', 'PilotesController@create');
        Route::post('/store', 'PilotesController@store');
    });
    Route::get("show", "PilotesController@show");
});

Route::group(['prefix' => 'stables'], function () {
    Route::group(['middleware' => 'checkRole'], function () {
        Route::get('/create', 'StablesController@create');
        Route::post('/store', 'StablesController@store');
    });
});

Route::group(['prefix' => 'results'], function () {
    Route::group(['middleware' => 'checkOrlando'], function () {
        Route::post('{gp}/results/{user}', 'ResultsController@bet');
    });
    Route::get('{gp}/results', 'ResultsController@show');
});

Route::get('/home', 'HomeController@index');
Route::group(['prefix' => 'errors'], function () {
   Route::get('/unauthorised', function () {
       return view('/errors/unauthorised');
   });
});
