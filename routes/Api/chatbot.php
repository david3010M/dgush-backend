<?php

use App\Http\Controllers\Chatbot\CatalogoController;
use App\Http\Controllers\Chatbot\DisponibilidadController;
use App\Http\Controllers\Chatbot\NegocioController;
use App\Http\Controllers\Chatbot\PreciosController;
use App\Http\Controllers\Chatbot\StockController;
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

// DISPONIBILIDAD
Route::prefix('disponibilidad')->name('disponibilidad.')->controller(DisponibilidadController::class)->group(function () {
    Route::post('/verificar', 'verificar')->name('verificar');
    Route::post('/tallas-producto', 'tallasProducto')->name('tallasProducto');
    Route::post('/tallas-de-color', 'tallasDeColor')->name('tallasDeColor');
    Route::post('/colores-de-talla', 'coloresDeTalla')->name('coloresDeTalla');
});

// CARRITO (solo la lectura de stock en MySQL)
Route::post('/carrito/verificar-stock', [DisponibilidadController::class, 'verificarStock'])->name('carrito.verificarStock');

// PRECIOS
Route::prefix('precios')->name('precios.')->controller(PreciosController::class)->group(function () {
    Route::post('/producto', 'producto')->name('producto');
    Route::post('/carrito', 'carrito')->name('carrito');
    Route::post('/validar-cupon', 'validarCupon')->name('validarCupon');
    Route::get('/cupones-activos', 'cuponesActivos')->name('cuponesActivos');
});

// CONSULTA NEGOCIO
Route::prefix('negocio')->name('negocio.')->group(function () {
    Route::get('/sedes', [NegocioController::class, 'sedes'])->name('sedes');
    Route::get('/zonas-delivery', [NegocioController::class, 'zonasDelivery'])->name('zonasDelivery');
    Route::post('/costo-envio', [NegocioController::class, 'costoEnvio'])->name('costoEnvio');
    // Alias de /precios/cupones-activos y /precios/validar-cupon (mismo SQL y respuesta).
    Route::get('/cupones', [PreciosController::class, 'cuponesActivos'])->name('cupones');
    Route::post('/validar-cupon', [PreciosController::class, 'validarCupon'])->name('validarCupon');
});

// STOCK (escritura: solo stock_reservations, nunca product_details.stock)
Route::prefix('stock')->name('stock.')->controller(StockController::class)->group(function () {
    Route::post('/reservar', 'reservar')->name('reservar');
    Route::post('/liberar', 'liberar')->name('liberar');
});
