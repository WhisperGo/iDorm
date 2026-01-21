<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/admin', function(){
    return view('admin.dashboard');
});

Route::get('/complaint', function(){
    return view('admin.complaint');
});

Route::get('/guess-report', function(){
    return view('admin.guess_report');
});

Route::get('/announcement', function(){
    return view('admin.announcement');
});

Route::get('/resident', function(){
    return view('admin.resident');
});

Route::get('/loan-report', function(){
    return view('admin.loan_report');
});

Route::get('/view/dapur', function(){
    return view('admin.guess_report');
});

Route::get('/guess-report', function(){
    return view('admin.guess_report');
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
