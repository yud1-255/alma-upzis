<?php

use App\Http\Controllers\ZakatController;
use App\Http\Controllers\MuzakkiController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('zakat', ZakatController::class);
    Route::resource('muzakki', MuzakkiController::class);
});

Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::post('/zakat/{id}/confirm', [ZakatController::class, 'confirmPayment'])->name('zakat.confirm');
});

require __DIR__ . '/auth.php';
