<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\HasPermissionController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SendInformationController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishItemController;
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

// ROUTES FOR AUTHENTICATION
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/product/image/{id}', [ProductController::class, 'uploadImages'])->name('product.images');
Route::get('/images', [ProductController::class, 'listImages'])->name('images.all');
Route::post('/deleteImage', [ProductController::class, 'deleteImage'])->name('images.delete');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// PUBLIC ROUTES
Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::post('/product/search', [ProductController::class, 'search'])->name('product.search');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product/subcategoryRelated/{id}', [ProductController::class, 'subcategoryRelated'])->name('product.subcategoryRelated');

Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');

Route::get('/subcategory', [SubcategoryController::class, 'index'])->name('subcategory.index');
Route::get('/subcategory/search', [SubcategoryController::class, 'search'])->name('subcategory.search');
Route::get('/subcategory/{id}', [SubcategoryController::class, 'show'])->name('subcategory.show');
Route::get('/subcategoryMostPopular', [SubcategoryController::class, 'mostPopular'])->name('subcategory.mostPopular');

//    PROVINCE
Route::get('/province', [ProvinceController::class, 'index'])->name('province.index');
Route::get('/province/{id}', [ProvinceController::class, 'show'])->name('province.show');

//    DISTRICT
Route::get('/district', [DistrictController::class, 'index'])->name('district.index');
Route::get('/district/{id}', [DistrictController::class, 'show'])->name('district.show');

// DEPARTMENT
Route::get('/department', [DepartmentController::class, 'index'])->name('department.index');
Route::get('/department/{id}', [DepartmentController::class, 'show'])->name('department.show');

Route::get('/filter/product', [FilterController::class, 'product'])->name('filter.product');

// ROUTES PROTECTED FOR AUTHENTICATED USERS WITH PERMISSIONS
Route::group(
    ['middleware' => ['auth:sanctum', 'checkAccess']],
    function () {
        //        GROUPMENU
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
        //        OPTIONMENU
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
        //    TYPEUSER
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
        //    USER
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
        //    ACCESS
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
        //    PERMISSION
        Route::resource('permission', PermissionController::class)->only(
            ['index', 'show']
        )->names(
            [
                'index' => 'permission.index',
                'show' => 'permission.show',
            ]
        );
        //    HASPERMISSION
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
        //    PRODUCT
        Route::resource('product', ProductController::class)->only(
            ['store', 'update', 'destroy']
        )->names(
            [
                'store' => 'product.store',
                'update' => 'product.update',
                'destroy' => 'product.destroy',
            ]
        );

        Route::get('/products', [ProductController::class, 'getAllProducts'])->name('product.all');
        Route::put('/product/setColors/{id}', [ProductController::class, 'setColors'])->name('product.colors');
        Route::put('/product/setSizes/{id}', [ProductController::class, 'setSizes'])->name('product.sizes');

        //    CATEGORY
        Route::resource('category', CategoryController::class)->only(
            ['store', 'update', 'destroy']
        )->names(
            [
                'store' => 'category.store',
                'update' => 'category.update',
                'destroy' => 'category.destroy',
            ]
        );

        //    SUBCATEGORY
        Route::resource('subcategory', SubcategoryController::class)->only(
            ['store', 'update', 'destroy']
        )->names(
            [
                'store' => 'subcategory.store',
                'update' => 'subcategory.update',
                'destroy' => 'subcategory.destroy',
            ]
        );

        //    COLOR
        Route::resource('color', ColorController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'color.index',
                'store' => 'color.store',
                'show' => 'color.show',
                'update' => 'color.update',
                'destroy' => 'color.destroy',
            ]
        );

        //    PRODUCT COLOR
        Route::resource('productcolor', ProductColorController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'productcolor.index',
                'store' => 'productcolor.store',
                'show' => 'productcolor.show',
                'update' => 'productcolor.update',
                'destroy' => 'productcolor.destroy',
            ]
        );

        //    SIZE
        Route::resource('size', SizeController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'size.index',
                'store' => 'size.store',
                'show' => 'size.show',
                'update' => 'size.update',
                'destroy' => 'size.destroy',
            ]
        );

        //    PRODUCT SIZE
        Route::resource('productsize', ProductSizeController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'productsize.index',
                'store' => 'productsize.store',
                'show' => 'productsize.show',
                'update' => 'productsize.update',
                'destroy' => 'productsize.destroy',
            ]
        );

        //    COMMENT
        Route::resource('comment', CommentController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'comment.index',
                'store' => 'comment.store',
                'show' => 'comment.show',
                'update' => 'comment.update',
                'destroy' => 'comment.destroy',
            ]
        );

//        COUPON
        Route::resource('coupon', CouponController::class)->only(
            ['index', 'store', 'show', 'update', 'destroy']
        )->names(
            [
                'index' => 'coupon.index',
                'store' => 'coupon.store',
                'show' => 'coupon.show',
                'update' => 'coupon.update',
                'destroy' => 'coupon.destroy',
            ]
        );

//        SEND INFORMATION
        Route::resource('sendinformation', SendInformationController::class)->only(
            ['index', 'store', 'show', 'update', 'destroy']
        )->names(
            [
                'index' => 'sendinformation.index',
                'store' => 'sendinformation.store',
                'show' => 'sendinformation.show',
                'update' => 'sendinformation.update',
                'destroy' => 'sendinformation.destroy',
            ]
        );

        //    ORDER
        Route::resource('order', OrderController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'order.index',
                'show' => 'order.show',
                'store' => 'order.store',
                'update' => 'order.update',
                'destroy' => 'order.destroy',
            ]
        );

//        WISH ITEM
        Route::resource('wishitem', WishItemController::class)->only(
            ['index', 'store', 'show', 'destroy']
        )->names(
            [
                'index' => 'wishitem.index',
                'store' => 'wishitem.store',
                'show' => 'wishitem.show',
                'destroy' => 'wishitem.destroy',
            ]
        );

    }
);
