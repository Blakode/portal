<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () { return view('welcome'); });


Route::group(['middleware' => ['jwt.auth', 'jwt.refresh']], function () {

    Route::apiResource('user', UserController::class)->names([
        'index' => 'user.list',
        'create' => 'user.create',
        'store' => 'user.store',
        'edit' => 'user.update',
        'delete' => 'user.delete'
        ]);

    Route::get('user-data', [UserController::class, 'getUserData']);
});

Route::post('register', [AuthController::class, 'register'])->name('user.register');
Route::post('login', [AuthController::class, 'login'])->name('user.login');