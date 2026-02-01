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
    // DASHBOARD & PROFILE
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile.edit');

    // --- SHARED ROUTES (Bisa diakses Resident, Admin, & Manager) ---
    
    // 1. View Announcement (Semua bisa lihat list)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
    
    // 2. View Jadwal Fasilitas (Semua bisa lihat siapa yang pinjam)
    // Nama rute diubah jadi 'facility.schedule' agar lebih umum
    // Cari baris ini di web.php dan ubah controllernya
    
    Route::get('/facility-schedule/{category}', [BookingController::class, 'showSchedule'])->name('facility.schedule');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/my-bookings', [BookingController::class, 'myPersonalHistory'])->name('booking.myBookings');
    // --- ROLE: RESIDENT ONLY ---
    Route::prefix('user')->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/booking/create/', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/complaint', [ComplaintController::class, 'index'])->name('penghuni.complaint');
        Route::get('/complaint/create', [ComplaintController::class, 'create'])->name('complaint.create');
        Route::post('/complaint/store', [ComplaintController::class, 'store'])->name('complaint.store');
        Route::get('/complaint/detail/{id}', [ComplaintController::class, 'show'])->name('penghuni.complaint.show');
        Route::post('/booking/upload/{booking}', [BookingController::class, 'uploadPhoto'])->name('booking.upload');
        
    });

    // --- ROLE: ADMIN & MANAGER (Akses bersama untuk Manage Complaint & Resident Data) ---
    // Kita pindahkan Resident Data ke sini agar Admin juga bisa melakukan FREEZE/UNFREEZE
    Route::middleware(['auth'])->group(function () {
        
        // Manajemen Resident (Bisa diakses Admin & Manager)
        Route::get('/residents', [ResidentController::class, 'index'])->name('pengelola.resident');
        Route::get('/residents', [ResidentController::class, 'index'])->name('admin.resident');
        Route::post('/residents/{user}/toggle-freeze', [ResidentController::class, 'toggleFreeze'])->name('pengelola.resident.freeze');
        Route::post('/residents/{user}/toggle-freeze', [ResidentController::class, 'toggleFreeze'])->name('admin.resident.freeze');

        // Manajemen Complaints
        Route::get('/complaints', [ComplaintController::class, 'adminIndex'])->name('admin.complaint');
        Route::get('/admin/complaints', [ComplaintController::class, 'index'])->name('admin.complaint');
        Route::post('/admin/booking/{booking}/{action}', [AdminBookingController::class, 'updateStatus'])->name('admin.booking.update');
    });

    // --- ROLE: MANAGER ONLY (Eksklusif Pengelola) ---
    Route::prefix('manager')->group(function () {
        // Laporan (Hanya Pengelola yang bisa lihat/print)
        Route::get('/reports', [ReportController::class, 'index'])->name('pengelola.report');
        Route::get('/residents', [ResidentController::class, 'index'])->name('pengelola.resident');
        
        // Halaman User Data
        Route::get('/resident-data', [ReportController::class, 'residentIndex'])->name('pengelola.report');
        
        // Halaman Loan Report
        Route::get('/loan-report', [ReportController::class, 'reportIndex'])->name('pengelola.report');

        // CRUD Announcement (Hanya Pengelola yang bisa Tambah/Edit/Hapus)
        Route::resource('announcements', AnnouncementController::class)->except(['index']);
    });
});

require __DIR__.'/auth.php';