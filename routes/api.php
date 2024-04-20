<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\GrupoMenuController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\TypeUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticación
//Route::post('/login', [LoginController::class, 'login']);
//Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
//
//Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
//Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware(['auth'])->group(function () {
Route::resource('grupomenu', GrupoMenuController::class);
Route::resource('optionmenu', OptionMenuController::class);
Route::resource('access', AccessController::class);
Route::resource('typeuser', TypeUserController::class);
//});



