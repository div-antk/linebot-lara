<?php

Route::get('/hello', 'LinebotController@index');

Route::get('/', function () {
    return view('welcome');
});
