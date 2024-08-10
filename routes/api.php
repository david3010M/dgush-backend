<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\GroupMenuController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OptionMenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailsController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WishItemController;
use App\Models\Image;
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
Route::post('/forgetPassword', [AuthController::class, 'forgetPassword'])->name('forgetPassword');
Route::post('/validateCode', [AuthController::class, 'validateCode'])->name('validateCode');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/changePassword', [AuthController::class, 'changePassword'])->name('changePassword');
});

// PUBLIC ROUTES
Route::get('/product', [ProductController::class, 'index'])->name('product.index');
//Route::post('/product', [ProductController::class, 'store'])->name('product.store');
Route::post('/product/search', [ProductController::class, 'search'])->name('product.search');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/productShow/{id}', [ProductController::class, 'productShow'])->name('product.productShow');
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
Route::get('/color', [ColorController::class, 'index'])->name('color.index');
Route::get('/size', [SizeController::class, 'index'])->name('size.index');

// BANNER
Route::get('/banner', [BannerController::class, 'index'])->name('banner.index');
Route::get('/banner/{id}', [BannerController::class, 'show'])->name('banner.show');

//VIDEO
Route::get('/video', [VideoController::class, 'index'])->name('video.index');
Route::post('/video', [VideoController::class, 'update'])->name('video.update');


// ROUTES PROTECTED FOR AUTHENTICATED USERS WITH PERMISSIONS
Route::group(['middleware' => ['auth:sanctum']], function () {

//    ROUTES JUST FOR ADMIN USERS
    Route::group(['middleware' => ['checkAccess']], function () {

        Route::get('/clients', [PersonController::class, 'index'])->name('person.index');
        Route::post('/product/image/{id}', [ImageController::class, 'uploadImages'])->name('product.images');

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

        //    PRODUCT
        Route::resource('product', ProductController::class)->only(
            ['store', 'destroy']
        )->names(
            [
                'store' => 'product.store',
                'destroy' => 'product.destroy',
            ]
        );

        Route::post('/productsOnSale', [ProductController::class, 'updateProductsOnSale'])->name('product.productsOnSale');
        Route::post('/updateProduct/{id}', [ProductController::class, 'update'])->name('product.update');
        Route::get('/products', [ProductController::class, 'getAllProducts'])->name('product.all');

        //    CATEGORY
        Route::resource('category', CategoryController::class)->only(
            ['store', 'destroy']
        )->names(
            [
                'store' => 'category.store',
                'destroy' => 'category.destroy',
            ]
        );
        Route::post('/category/{id}', [CategoryController::class, 'update'])->name('category.update');


        //    SUBCATEGORY
        Route::resource('subcategory', SubcategoryController::class)->only(
            ['store', 'destroy']
        )->names(
            [
                'update' => 'subcategory.update',
                'destroy' => 'subcategory.destroy',
            ]
        );
        Route::post('/subcategory/{id}', [SubcategoryController::class, 'update'])->name('subcategory.update');

        //    COLOR
        Route::resource('color', ColorController::class)->only(
            ['show', 'store', 'update', 'destroy']
        )->names(
            [
                'store' => 'color.store',
                'show' => 'color.show',
                'update' => 'color.update',
                'destroy' => 'color.destroy',
            ]
        );

        //    SIZE
        Route::resource('size', SizeController::class)->only(
            ['show', 'store', 'update', 'destroy']
        )->names(
            [
                'store' => 'size.store',
                'show' => 'size.show',
                'update' => 'size.update',
                'destroy' => 'size.destroy',
            ]
        );

//        ORDER ADMIN
        Route::post('/order/search', [OrderController::class, 'search'])->name('order.search');
        Route::post('/order/updateStatus/{id}', [OrderController::class, 'updateStatus'])->name('order.updateStatus');
        Route::get('/orderStatus', [OrderController::class, 'orderStatus'])->name('order.orderStatus');
        Route::get('/dashboardOrders', [OrderController::class, 'dashboardOrders'])->name('order.dashboard');

//        PRODUCT DETAILS
        Route::resource('productdetails', ProductDetailsController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'productdetails.index',
                'store' => 'productdetails.store',
                'show' => 'productdetails.show',
                'update' => 'productdetails.update',
                'destroy' => 'productdetails.destroy',
            ]
        );

        Route::post('/productdetails/search', [ProductDetailsController::class, 'search'])->name('productdetails.search');

//        IMAGES
        Route::get('/images', [ImageController::class, 'listImages'])->name('images.all');
        Route::delete('/deleteDirectoryProduct', [ImageController::class, 'deleteDirectoryProduct'])->name('images.deleteDirectoryProduct');

//        BANNER
        Route::resource('banner', BannerController::class)->only(
            ['store', 'destroy']
        )->names(
            [
                'store' => 'banner.store',
                'destroy' => 'banner.destroy',
            ]
        );
        Route::post('/banner-video', [BannerController::class, 'storeVideo'])->name('banner.video');

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

//        DISTRICT
        Route::resource('district', DistrictController::class)->only(
            ['store', 'update', 'destroy']
        )->names(
            [
                'store' => 'district.store',
                'update' => 'district.update',
                'destroy' => 'district.destroy',
            ]
        );

//        SEDE
        Route::resource('sede', SedeController::class)->only(
            ['index', 'show', 'store', 'update', 'destroy']
        )->names(
            [
                'index' => 'sede.index',
                'store' => 'sede.store',
                'show' => 'sede.show',
                'update' => 'sede.update',
                'destroy' => 'sede.destroy',
            ]
        );
    });

//    ORDER CLIENT
    Route::post('/confirmOrder/{id}', [OrderController::class, 'confirmOrder'])->name('order.confirm');
    Route::post('/applyCouponToOrder/{id}', [OrderController::class, 'applyCoupon'])->name('order.applyCoupon');
    Route::post('/cancelOrder/{id}', [OrderController::class, 'cancelOrder'])->name('order.cancel');
    Route::post('/setOrderDistrict/{id}', [OrderController::class, 'setOrderDistrict'])->name('order.setOrderDistrict');

//    WISH ITEM
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

    //    ORDER
    Route::resource('order', OrderController::class)->only(
        ['index', 'show', 'store', 'update']
    )->names(
        [
            'index' => 'order.index',
            'show' => 'order.show',
            'store' => 'order.store',
            'update' => 'order.update',
        ]
    );

    //    COUPON
    Route::resource('coupon', CouponController::class)->only(
        ['index', 'show']
    )->names(
        [
            'index' => 'coupon.index',
            'show' => 'coupon.show',
        ]
    );

    //    WISH ITEM
    Route::resource('wishitem', WishItemController::class)->only(
        ['index', 'show', 'destroy']
    )->names(
        [
            'index' => 'wishitem.index',
            'show' => 'wishitem.show',
            'destroy' => 'wishitem.destroy',
        ]
    );

    //    PERSON
    Route::get('/person', [PersonController::class, 'show'])->name('person.show');
    Route::put('/person', [PersonController::class, 'update'])->name('person.update');


}
);
