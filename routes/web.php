<?php

use App\Http\Controllers\Admin\StructureController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportsController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\MaterialsController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StaffsController;
use App\Http\Controllers\StructuresController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('change-language/{lang}', [LangController::class,'changeLang'])->name('change-language');

Route::prefix('')->group(function () {
    Route::get('/', [UsersController::class,'getLogin']);
    Route::get('login', [UsersController::class,'getLogin']);
    Route::post('login', [UsersController::class,'postLogin']);
    Route::get('logout', [UsersController::class,'getLogout'])->name('logout');
    Route::get('forgot-password', [UsersController::class,'forgotPassword']);
    Route::post('forgot-password', [UsersController::class,'getNewPassword']);

});

Route::group(['middleware' => ['ChangeLanguage', 'CheckLogin','WebConfig']], function () {

    Route::prefix('home')->group(function () {
        Route::get('', [HomeController::class,'index'])->name('home');
    });

    Route::prefix('structure')->group(function () {
        Route::get('', [StructuresController::class,'index']);
        Route::get('/add/{pid?}', [StructuresController::class,'addNew']);
        Route::post('/add', [StructuresController::class,'store']);
        Route::get('/edit/{id?}', [StructuresController::class,'edit']);
        Route::put('/edit', [StructuresController::class,'update']);
        Route::delete('/delete/{id}', [StructuresController::class,'destroy']);
    });

    Route::prefix('multi-languages')->group(function () {
        Route::get('languages', [LanguagesController::class,'index']);
        Route::get('languages/add/{pid?}', [LanguagesController::class,'addNew']);
        Route::post('languages/add', [LanguagesController::class,'store']);
        Route::get('languages/edit/{id?}', [LanguagesController::class,'edit']);
        Route::put('languages/edit', [LanguagesController::class,'update']);
        Route::delete('languages/delete/{id}', [LanguagesController::class,'destroy']);

        Route::get('languages/change-status/{id}', [LanguagesController::class,'changeStatus']);

        Route::get('translations', [LanguagesController::class,'translateList']);
        Route::get('translations/edit/{id}', [LanguagesController::class,'translateEdit']);
        Route::put('translations/edit', [LanguagesController::class,'translateUpdate']);
    });

    Route::prefix('users/users-list')->group(function () {
        Route::get('', [UsersController::class,'index']);
        Route::get('/add', [UsersController::class,'addNew']);
        Route::post('/add', [UsersController::class,'store']);
        Route::get('/edit/{id?}', [UsersController::class,'edit']);
        Route::put('/edit', [UsersController::class,'update']);
        Route::get('/change-status/{id}', [UsersController::class,'changeStatus']);
        Route::delete('/delete/{id}', [UsersController::class,'destroy']);
    });

    Route::prefix('users/users-group')->group(function () {
        Route::get('', [UsersController::class,'listGroup']);
        Route::get('/add', [UsersController::class,'addNewGroup']);
        Route::post('/add', [UsersController::class,'storeGroup']);
        Route::get('/edit/{id?}', [UsersController::class,'editGroup']);
        Route::put('/edit', [UsersController::class,'updateGroup']);
        Route::get('/change-status/{id}', [UsersController::class,'changeStatusGroup']);
        Route::delete('/delete/{id}', [UsersController::class,'destroyGroup']);
    });

    Route::prefix('account')->group(function () {
        Route::get('', [UsersController::class,'show']);
    });

    //Expand

    Route::prefix('materials')->group(function () {
        Route::get('', [MaterialsController::class,'index']);
        Route::get('/add', [MaterialsController::class,'addNew']);
        Route::post('/add', [MaterialsController::class,'store']);
        Route::get('/edit/{id}', [MaterialsController::class,'edit']);
        Route::put('/edit/{id}', [MaterialsController::class,'update']);
        Route::delete('/delete/{id}', [MaterialsController::class,'destroy']);
    });

    Route::prefix('products')->group(function () {
        Route::get('', [ProductsController::class,'index']);
        Route::get('/add/{pid?}', [ProductsController::class,'addNew']);
        Route::post('/add', [ProductsController::class,'store']);
        Route::get('/show/{id}', [ProductsController::class,'show']);
        Route::get('/edit/{id}', [ProductsController::class,'edit']);
        Route::put('/edit/{id}', [ProductsController::class,'update']);
        Route::delete('/delete/{id}', [ProductsController::class,'destroy']);
    });

    Route::prefix('input')->group(function () {
        Route::get('', [InputController::class,'index']);
        Route::get('/add/{pid?}', [InputController::class,'addNew']);
        Route::post('/add', [InputController::class,'store']);
        Route::get('/edit/{id}', [InputController::class,'edit']);
        Route::put('/edit/{id}', [InputController::class,'update']);
        Route::delete('/delete/{id}', [InputController::class,'destroy']);
    });

    Route::prefix('output')->group(function () {
        Route::get('', [OutputController::class,'index']);
        Route::get('/add/{pid?}', [OutputController::class,'addNew']);
        Route::post('/add', [OutputController::class,'store']);
        Route::get('/edit/{id}', [OutputController::class,'edit']);
        Route::put('/edit/{id}', [OutputController::class,'update']);
        Route::delete('/delete/{id}', [OutputController::class,'destroy']);
    });

    Route::prefix('customers')->group(function () {
        Route::get('', [CustomersController::class,'index']);
        Route::get('/add/{pid?}', [CustomersController::class,'addNew']);
        Route::post('/add', [CustomersController::class,'store']);
        Route::get('/edit/{id}', [CustomersController::class,'edit']);
        Route::put('/edit/{id}', [CustomersController::class,'update']);
        Route::delete('/delete/{id}', [CustomersController::class,'destroy']);
    });

    Route::prefix('staffs')->group(function () {
        Route::get('', [StaffsController::class,'index']);
        Route::get('/add/{pid?}', [StaffsController::class,'addNew']);
        Route::post('/add', [StaffsController::class,'store']);
        Route::get('/edit/{id}', [StaffsController::class,'edit']);
        Route::put('/edit/{id}', [StaffsController::class,'update']);
        Route::delete('/delete/{id}', [StaffsController::class,'destroy']);
    });

    Route::prefix('ajax')->group(function () {
        Route::post('/input-output', [AjaxController::class,'input_output']);
    });
});