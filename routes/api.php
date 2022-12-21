<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->group(function () {

    // User Authenticated Route Starts Here

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User Authenticated Route Ends Here


    // Category Authenticated Route Starts Here

    Route::post('/create/category/',[App\Http\Controllers\Dashboard\CategoryController::class, 'createCategory']);
    Route::put('/update/category/by/category-id/{id}', [App\Http\Controllers\Dashboard\CategoryController::class, 'updateCategoryByCategoryRdbmsId']);
    Route::delete('/delete/category/by/category-id/{id}',[App\Http\Controllers\Dashboard\CategoryController::class,'deleteCategoryByCategoryRdbmsId']);
    Route::get('/find/all/categories/',[App\Http\Controllers\Dashboard\CategoryController::class, 'findAllCategory']);

    // Category Authenticated Route Ends Here


    // Article Authenticated Route Starts Here

    Route::post('/create/article/',[App\Http\Controllers\Dashboard\ArticleController::class, 'createArticle']);
    Route::put('/update/article/by/article-id/{id}',[App\Http\Controllers\Dashboard\ArticleController::class, 'updateArticleByArticleRdbmsId']);
    Route::delete('/delete/article/by/article-id/{id}',[App\Http\Controllers\Dashboard\ArticleController::class,'deleteArticleByArticleRdbmsId']);

    // Article Authenticated Route Ends Here

});

    // User Unauthenticated Route Starts Here

    Route::post('/user/login', [\App\Http\Controllers\Dashboard\Auth\LoginController::class, 'userLogin']);

    Route::post('/user/register', [\App\Http\Controllers\Dashboard\Auth\RegisterController::class, 'userRegister']);

    // User Unauthenticated Route Ends Here


    // Category Unauthenticated Route Starts Here

    Route::get('/find/category/by/category-id/{id}/',[App\Http\Controllers\Dashboard\CategoryController::class, 'findCategoryByCategoryRdbmsId']);

    Route::get('/search/category/by/keyword/{keyword}',[App\Http\Controllers\Dashboard\CategoryController::class,'searchCategoryByKeyword']);

    // Category Unauthenticated Route Ends Here


    // Article Unauthenticated Route Starts Here

    Route::get('/find/all/articles/',[App\Http\Controllers\Dashboard\ArticleController::class, 'findAllArticles']);

    Route::get('/find/article/by/article-id/{id}',[App\Http\Controllers\Dashboard\ArticleController::class, 'findArticleByArticleRdbmsId']);

    Route::get('/search/article/by/keyword/{keyword}',[App\Http\Controllers\Dashboard\ArticleController::class,'searchArticleByKeyword']);

    // Article Unauthenticated Route Ends Here

