<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AnnouncementController;

// 1. GUEST ROUTES
Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {
    
    // 1. DASHBOARD & PROFILE
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // 2. SHARED ROUTES (Semua Role: Resident, Admin, Manager)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::get('/facility-schedule/{category}', [BookingController::class, 'showSchedule'])->name('facility.schedule');
    Route::get('/my-bookings', [BookingController::class, 'myPersonalHistory'])->name('booking.myBookings');

    // 3. ANNOUNCEMENT MANAGEMENT (Hanya Admin & Manager)
    // Ditaruh di ATAS rute {id} agar tidak 404
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // 4. ANNOUNCEMENT SHOW (Parameter ditaruh paling bawah)
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');

    // 5. ROLE: RESIDENT ONLY
    Route::prefix('user')->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::get('/booking/create/', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
        Route::post('/booking/upload/{booking}', [BookingController::class, 'uploadPhoto'])->name('booking.upload');
        
        Route::get('/complaint', [ComplaintController::class, 'index'])->name('penghuni.complaint');
        Route::get('/complaint/create', [ComplaintController::class, 'create'])->name('complaint.create');
        Route::post('/complaint/store', [ComplaintController::class, 'store'])->name('complaint.store');
        Route::get('/complaint/detail/{id}', [ComplaintController::class, 'show'])->name('penghuni.complaint.show');
    });

    // 6. ROLE: ADMIN & MANAGER (Pengelola)
    Route::get('/residents', [ResidentController::class, 'index'])->name('admin.resident'); // Satu rute cukup
    Route::post('/residents/{user}/toggle-freeze', [ResidentController::class, 'toggleFreeze'])->name('admin.resident.freeze');
    
    Route::get('/complaints', [ComplaintController::class, 'adminIndex'])->name('admin.complaint');
    Route::post('/admin/booking/{booking}/{action}', [AdminBookingController::class, 'updateStatus'])->name('admin.booking.update');

    // 7. ROLE: MANAGER ONLY (Reports)
    Route::prefix('manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('pengelola.report');
        Route::get('/resident-data', [ReportController::class, 'residentIndex'])->name('pengelola.resident_data');
        Route::get('/loan-report', [ReportController::class, 'reportIndex'])->name('pengelola.loan_report');
    });
});

require __DIR__.'/auth.php';