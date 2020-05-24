<?php

Route::get('/hello', 'LineBotController@index');

Route::get('/', function () {
    return view('welcome');
});
