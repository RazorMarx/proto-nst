<?php

use App\Http\Controllers\FilesController;
use App\Http\Controllers\ProfileController;
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
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/sendfile', [FilesController::class, 'store'] )->name('sendfile');
    Route::get('/files', [FilesController::class, 'index'] )->name('getfiles');
    Route::get('/dl/{name}', [FilesController::class, 'download'] )->name('files.download');
    Route::get('/getfile/{name}', [FilesController::class, 'serve'] )->name('files.getit');
    Route::delete('/deletefile/{name}', [FilesController::class, 'destroy'] )->name('files.destroy');
    Route::delete('/deletefilepart/{name}', [FilesController::class, 'destroyparts'] )->name('files.destroyparts');
});

require __DIR__.'/auth.php';