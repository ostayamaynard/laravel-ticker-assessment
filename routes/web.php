<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TickerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [TickerController::class, 'index'])->name('ticker.index');
Route::post('/search', [TickerController::class, 'search'])->name('ticker.search');
