<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/main', function(){
    return view('pengelola.layouts');
});

Route::get('/announcement', function(){
    return view('pengelola.announcement');
});

Route::get('/resident', function(){
    return view('pengelola.resident');
});

Route::get('/view/dapur', function(){
    return view('pengelola.dapur');
});

Route::get('/view/mesin-cuci', function(){
    return view('pengelola.mesin-cuci');
});

Route::get('/view/theatre', function(){
    return view('pengelola.teater');
});

Route::get('/view/sergun', function(){
    return view('pengelola.sergun');
});

Route::get('/view/cws', function(){
    return view('pengelola.cws');
});

Route::get('/loan-report', function(){
    return view('pengelola.loan_report');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
