<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProgramController;



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

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth', 'admin'])->group(function () {
    // Add your admin-only routes here
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');
    Route::post('change-role/{user}', [HomeController::class, 'changeRole'])->name('changeRole');
    // Route::('/logs', [LogController::class, 'index'])->name('logs');
    Route::resource('log', LogController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('program', ProgramController::class);
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');


Auth::routes();

// Route::get('/home', [HomeController::class, 'index'])->name('home');
// Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');
// Route::post('change-role/{user}', [HomeController::class, 'changeRole'])->name('changeRole');
// // Route::('/logs', [LogController::class, 'index'])->name('logs');
// Route::resource('log', LogController::class);