<?php

use App\Mail\ForgetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', function () {
    Mail::to('hvaldiviezos@unprg.edu.pe')
        ->send(new ForgetPassword());
});

Route::get('/mailPrueba', function () {
    return view('mail.forgetPassword');
});
