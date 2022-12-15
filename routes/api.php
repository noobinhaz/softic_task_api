<?php

use Illuminate\Http\Request;
use App\Http\Controllers\RoleControl;
use App\Http\Controllers\UserControl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashBoardControl;

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

Route::post('/register', [UserControl::class, 'register']);
Route::post('/login', [UserControl::class, 'authenticate']);

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('/roles', [RoleControl::class, 'index']);
    Route::get('/users', [UserControl::class, 'index']);
    Route::get('/users/{id}', [UserControl::class, 'show']);
    Route::get('/logout', [UserControl::class, 'logout']);
    Route::get('/dashboard', [DashBoardControl::class, 'dashboard']);
    Route::get('/updateCredentials', [DashBoardControl::class, 'updateCredentials']);
    Route::get('/myAffiliateList', [DashBoardControl::class, 'myAffiliateList']);
    Route::post('/admin/dashboard', [DashBoardControl::class, 'adminDashboard']);
    
});

Route::get('/', function(){
    return "hello world!";
});
