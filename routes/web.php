<?php

use App\Http\Controllers\ZakatController;
use App\Http\Controllers\MuzakkiController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\RoleController;
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

Route::middleware(['auth', 'role:administrator,upzis'])->group(function () {
    Route::get('/zakat/muzakki_list', [ZakatController::class, 'muzakkiList'])->name('zakat.muzakkiList');
    Route::get('/zakat/muzakki_recap', [ZakatController::class, 'muzakkiRecap'])->name('zakat.muzakkiRecap');
    Route::get('/zakat/online_payments', [ZakatController::class, 'onlinePayments'])->name('zakat.onlinePayments');
    Route::post('/zakat/{id}/confirm', [ZakatController::class, 'confirmPayment'])->name('zakat.confirm');
    Route::get('/family/search', [FamilyController::class, 'search'])->name('family.search');
});

Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles/assign', [RoleController::class, 'assign'])->name('roles.assign');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('zakat', ZakatController::class);
    Route::resource('muzakki', MuzakkiController::class);
    Route::resource('family', FamilyController::class);
});



require __DIR__ . '/auth.php';
