<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Route;

//Rutas para la alimentación de bd desde 360
Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('getdata-category', [CategoryController::class, 'getCategories']);
    Route::get('getdata-subcategory', [SubcategoryController::class, 'getSubCategories']);
    Route::get('getdata-color', [ColorController::class, 'getcolors']);
    Route::get('getdata-size', [SizeController::class, 'getsizes']);
    Route::get('getdata-products', [ProductController::class, 'getproducts']);
    
});
