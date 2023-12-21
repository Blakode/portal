<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
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

Route::get('/', function () { return view('welcome'); });

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['jwt.auth','api']], function () {

    Route::apiResource('user', UserController::class)->names([
        'index' => 'users',
        'store' => 'user.store',
        'update' => 'user.update',
        'delete' => 'user.destroy'
        ])->middleware('admin.check', ['only' => ['store', 'index']]);

    Route::apiResource('grade', GradeController::class)->names([
        'index' => 'grades',
        'store' => 'grade.store',
        'update' => 'grade.update',
        'delete' => 'grade.delete'
        ]);
    
    Route::get('user-data', [UserController::class, 'getUserData']);
    Route::delete('/delete-user', [UserController::class, 'deleteUser'])->middleware('admin.check'); 
});

