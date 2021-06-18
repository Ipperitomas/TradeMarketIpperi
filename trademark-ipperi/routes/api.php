<?php

use App\Http\Controllers\Articles;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RubrosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('products', ArticlesController::class);
Route::resource('rubros', RubrosController::class);
Route::resource('inventory', InventoryController::class);
// Route::resource('registro', RubrosController::class);
