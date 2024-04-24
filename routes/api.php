<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\HasPermissionController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\PermissionController;
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
Route::group(
    ['middleware' => ['auth:sanctum', 'checkAccess']], function () {
    Route::resource('groupmenu', GroupMenuController::class)->only(
        ['index', 'show', 'store', 'update', 'destroy']
    )->names(
        [
            'index' => 'groupmenu.index',
            'store' => 'groupmenu.store',
            'show' => 'groupmenu.show',
            'update' => 'groupmenu.update',
            'destroy' => 'groupmenu.destroy',
        ]
    );
    Route::resource('optionmenu', OptionMenuController::class)->only(
        ['index', 'show', 'store', 'update', 'destroy']
    )->names(
        [
            'index' => 'optionmenu.index',
            'store' => 'optionmenu.store',
            'show' => 'optionmenu.show',
            'update' => 'optionmenu.update',
            'destroy' => 'optionmenu.destroy',
        ]
    );
    Route::resource('typeuser', TypeUserController::class)->only(
        ['index', 'show', 'store', 'update', 'destroy']
    )->names(
        [
            'index' => 'typeuser.index',
            'store' => 'typeuser.store',
            'show' => 'typeuser.show',
            'update' => 'typeuser.update',
            'destroy' => 'typeuser.destroy',
        ]
    );
    Route::resource('user', UserController::class)->only(
        ['index', 'show', 'store', 'update', 'destroy']
    )->names(
        [
            'index' => 'user.index',
            'store' => 'user.store',
            'show' => 'user.show',
            'update' => 'user.update',
            'destroy' => 'user.destroy',
        ]
    );
    Route::resource('access', AccessController::class)->only(
        ['index', 'show', 'store', 'update', 'destroy']
    )->names(
        [
            'index' => 'access.index',
            'store' => 'access.store',
            'show' => 'access.show',
            'update' => 'access.update',
            'destroy' => 'access.destroy',
        ]
    );
    Route::resource('permission', PermissionController::class)->only(
        ['index', 'show']
    )->names(
        [
            'index' => 'permission.index',
//            'store' => 'permission.store',
            'show' => 'permission.show',
//            'update' => 'permission.update',
//            'destroy' => 'permission.destroy',
        ]
    );
    Route::resource('haspermission', HasPermissionController::class)->only(
        ['index', 'show', 'store', 'update', 'destroy']
    )->names(
        [
            'index' => 'haspermission.index',
            'store' => 'haspermission.store',
            'show' => 'haspermission.show',
            'update' => 'haspermission.update',
            'destroy' => 'haspermission.destroy',
        ]
    );
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




