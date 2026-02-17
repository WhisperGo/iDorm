<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\SuspendController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 1. GUEST ROUTES
Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {

    // 1. DASHBOARD & PROFILE
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/edit/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // 2. SHARED ROUTES (Semua Role: Resident, Admin, Manager)
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::get('/facility-schedule/{category}', [BookingController::class, 'showSchedule'])->name('facility.schedule');
    Route::get('/my-bookings', [BookingController::class, 'myPersonalHistory'])->name('booking.my_bookings');

    // 3. ANNOUNCEMENT MANAGEMENT (Hanya Admin & Manager)
    // Ditaruh di ATAS rute {id} agar tidak 404
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // 4. ANNOUNCEMENT SHOW (Parameter ditaruh paling bawah)
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');

    Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction.index');
    Route::post('/prediction', [PredictionController::class, 'store'])->name('prediction.store');

    // 5. ROLE: RESIDENT ONLY
    Route::prefix('user')->group(function () {
        Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
        Route::get('/booking/create/', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
        Route::post('/booking/{booking}/early-release', [BookingController::class, 'earlyRelease'])->name('booking.earlyRelease');
        Route::post('/booking/{booking}/upload-photo', [BookingController::class, 'uploadPhoto'])->name('booking.upload');

        Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint.index');
        Route::get('/complaint/create', [ComplaintController::class, 'create'])->name('complaint.create');
        Route::post('/complaint/store', [ComplaintController::class, 'store'])->name('complaint.store');
        Route::get('/complaint/detail/{id}', [ComplaintController::class, 'show'])->name('penghuni.complaint.show');
    });

    // 6. ROLE: ADMIN
    // Route untuk nampilin form edit
    Route::prefix('admin')->group(function () {
        // Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        // Route::put('/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');

        Route::get('/residents', [ResidentController::class, 'index'])->name('admin.resident'); // Satu rute cukup
        Route::post('/residents/{user}/toggle-freeze', [ResidentController::class, 'toggleFreeze'])->name('admin.resident.freeze');

        Route::post('/booking/{booking}/{action}', [AdminBookingController::class, 'updateStatus'])->name('admin.booking.update');
        Route::put('/booking/{booking}/{action}', [BookingController::class, 'adminAction'])->name('admin.booking.action');

        Route::get('/complaint', [ComplaintController::class, 'index'])->name('admin.complaint');
        Route::get('/complaint/create', [ComplaintController::class, 'create'])->name('complaint.create');
        Route::post('/complaint/store', [ComplaintController::class, 'store'])->name('complaint.store');
        Route::get('/complaint/{id}', [ComplaintController::class, 'show'])->name('admin.complaint.show');
    });


    // 7. ROLE: MANAGER ONLY (Reports)
    Route::prefix('manager')->group(function () {
        // Tambahkan baris ini!
        // Route::get('/admin/residents', [ComplaintController::class, 'adminIndex'])->name('admin.resident.index');
        Route::get('/resident-data', [UserController::class, 'residentIndex'])->name('pengelola.resident');
        
        Route::get('/admins', [UserController::class, 'adminIndex'])->name('manager.admins.index');
        
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');

        // Rute Tambah Resident
        Route::get('/residents/create', [UserController::class, 'createResident'])->name('manager.residents.create');
        Route::post('/residents/store', [UserController::class, 'storeResident'])->name('manager.residents.store');
        
        // Route Hapus Resident
        Route::delete('/residents/{id}', [UserController::class, 'destroyResident'])->name('manager.residents.destroy');
        
        // Route Tambah Admin
        Route::get('/admins/create', [UserController::class, 'createAdmin'])->name('manager.admins.create');
        Route::post('/admins/store', [UserController::class, 'storeAdmin'])->name('manager.admins.store');

        // Route Hapus Admin
        Route::delete('/admins/{id}', [UserController::class, 'destroyAdmin'])->name('manager.admins.destroy');
        // Resident Data (role_id = 3)
        // Route::get('/residents', [UserController::class, 'residentIndex'])->name('manager.residents.index');

        // Admin Data (role_id = 2)

        // Route Edit & Update tetap satu karena fungsinya sama (berdasarkan ID)

        // SEBELUMNYA (Mungkin seperti ini atau mirip)
        Route::get('/reports', [ReportController::class, 'index'])->name('pengelola.report');
        // Route::get('/admins', [UserController::class, 'adminIndex'])->name('admin.resident');
        Route::get('/loan-report', [ReportController::class, 'reportIndex'])->name('pengelola.loan_report');
        Route::get('/complaints', [ComplaintController::class, 'adminIndex'])->name('pengelola.complaint');
        Route::get('/complaints/{id}', [ComplaintController::class, 'showManager'])->name('pengelola.complaint.showPengelolaOnly');
        Route::patch('/complaints/{id}/status', [ComplaintController::class, 'updateStatus'])->name('pengelola.updateStatus');
        Route::get('/report/excel', [ReportController::class, 'exportExcel'])->name('pengelola.loan_report.excel');
        Route::get('/report/pdf', [ReportController::class, 'exportPdf'])->name('pengelola.loan_report.pdf');
    });

    // Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('/suspensions', [SuspendController::class, 'index'])->name('suspensions.index');
    Route::post('/suspensions', [SuspendController::class, 'store'])->name('suspensions.store');
    Route::delete('/suspensions/{id}', [SuspendController::class, 'destroy'])->name('suspensions.destroy');

    Route::put('/booking/{id}/cleanliness', [App\Http\Controllers\BookingController::class, 'updateCleanliness'])
    ->name('booking.cleanliness.update');
});

require __DIR__ . '/auth.php';