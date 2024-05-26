<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/token', function (Request $request) {
    return csrf_token();
});

require __DIR__.'/auth.php';
