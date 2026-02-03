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
        Route::post('/booking/{booking}/early-release', [BookingController::class, 'earlyRelease'])->name('booking.earlyRelease');
        Route::post('/booking/{booking}/upload-photo', [BookingController::class, 'uploadPhoto'])->name('booking.upload');
        
        Route::get('/complaint', [ComplaintController::class, 'index'])->name('penghuni.complaint');
        Route::get('/complaint/create', [ComplaintController::class, 'create'])->name('complaint.create');
        Route::post('/complaint/store', [ComplaintController::class, 'store'])->name('complaint.store');
        Route::get('/complaint/detail/{id}', [ComplaintController::class, 'showResident'])->name('penghuni.complaint.show');
    });

    // 6. ROLE: ADMIN & MANAGER (Pengelola)
    // Route untuk nampilin form edit
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');

    // Route untuk PROSES UPDATE (Ini yang tadi hilang atau salah panggil)
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    
    Route::get('/residents', [ResidentController::class, 'index'])->name('admin.resident'); // Satu rute cukup
    Route::post('/residents/{user}/toggle-freeze', [ResidentController::class, 'toggleFreeze'])->name('admin.resident.freeze');
    
    Route::get('/complaints', [ComplaintController::class, 'adminIndex'])->name('admin.complaint');
    Route::post('/admin/booking/{booking}/{action}', [AdminBookingController::class, 'updateStatus'])->name('admin.booking.update');
    
    Route::put('/admin/booking/{booking}/{action}', [BookingController::class, 'adminAction'])->name('admin.booking.action');
    Route::patch('/complaints/{id}/status', [ComplaintController::class, 'updateStatus'])->name('admin.complaint.updateStatus');
    Route::get('/complaints/{id}', [ComplaintController::class, 'showAdmin'])->name('admin.complaint.showAdminOnly');
    

    // 7. ROLE: MANAGER ONLY (Reports)
    Route::prefix('manager')->group(function () {
        // Tambahkan baris ini!
    Route::get('/admin/residents', [ComplaintController::class, 'adminIndex'])->name('admin.resident.index');
    
    // Rute update yang kita buat tadi juga pastikan ada di sini
    Route::put('/admin/resident/{id}/update', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/reports', [ReportController::class, 'index'])->name('pengelola.report');
        Route::get('/resident-data', [ResidentController::class, 'index'])->name('pengelola.resident');
        Route::get('/loan-report', [ReportController::class, 'reportIndex'])->name('pengelola.loan_report');
    });
});

require __DIR__.'/auth.php';