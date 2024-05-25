<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Api\v1\PostController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-db-connection', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection is successful';
    } catch (\Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});

// Route::get(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\v1'], function () {
//     Route::apiResource('/post', PostController::class);
// });

Route::prefix('api/v1')->group(function () {
    Route::get('/posts', [PostController::class, 'posts']);
});
