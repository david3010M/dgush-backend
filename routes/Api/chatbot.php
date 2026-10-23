<?php

use App\Http\Controllers\Chatbot\CatalogoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API del chatbot de pedidos — prefijo /api/chatbot
|--------------------------------------------------------------------------
|
| Solo lectura. Contrato: AGENTS/DGush_API_Especificacion_Tecnica.md
| Middleware (RouteServiceProvider): throttle:chatbot + chatbot.key
|
*/

// CATÁLOGO
Route::prefix('catalogo')->name('catalogo.')->controller(CatalogoController::class)->group(function () {
    Route::post('/buscar-categoria', 'buscarCategoria')->name('buscarCategoria');
    Route::post('/buscar-nombre', 'buscarNombre')->name('buscarNombre');
    Route::post('/buscar-palabras', 'buscarPalabras')->name('buscarPalabras');
    Route::post('/variantes', 'variantes')->name('variantes');
    Route::post('/multifiltro', 'multifiltro')->name('multifiltro');
    Route::get('/liquidacion', 'liquidacion')->name('liquidacion');
    Route::post('/fotos', 'fotos')->name('fotos');
    Route::post('/alternativas', 'alternativas')->name('alternativas');
});
