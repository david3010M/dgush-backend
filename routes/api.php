<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\UserController;
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

// Rutas para el inicio de sesión y cierre de sesión
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Rutas protegidas con Sanctum
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('groupmenu', GroupMenuController::class);
    Route::resource('optionmenu', OptionMenuController::class);
    Route::resource('typeuser', TypeUserController::class);
    Route::resource('user', UserController::class);
    Route::resource('access', AccessController::class);
});



/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="User unique ID"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="User name"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="User email"
 *     )
 * )
 */




