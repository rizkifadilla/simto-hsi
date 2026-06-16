<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/dashboard-general-dashboard');

/*
|--------------------------------------------------------------------------
| Auth (Guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::middleware('role:admin')->group(function () {

        // Dashboard
        Route::get('/dashboard-general-dashboard', [DashboardController::class, 'index']);

        Route::get('/dashboard-ecommerce-dashboard', function () {
            return view('pages.dashboard-ecommerce-dashboard', [
                'type_menu' => 'dashboard'
            ]);
        });

        // Master Employee
        Route::get('/employees/template', [EmployeeController::class, 'downloadTemplate'])
            ->name('employees.template');

        Route::post('/employees/import', [EmployeeController::class, 'import'])
            ->name('employees.import');
        Route::get('/employee-distance-setting', [EmployeeController::class, 'distanceSetting'])
            ->name('employees.distance-setting');
        Route::post('/employee-distance-setting', [EmployeeController::class, 'updateDistanceSetting'])
            ->name('employees.distance-setting.update');
        Route::get('/master-employee', [EmployeeController::class, 'index'])
            ->name('employees.index');
        Route::post('/employees/{id}/reset-face', [EmployeeController::class, 'resetFace'])
            ->name('employees.reset-face');
        Route::get('employees/{id}/attendance', [EmployeeController::class, 'attendance'])
            ->name('employees.attendance');
        Route::resource('employees', EmployeeController::class)->except(['show']);

        Route::post('attendance/{id}/update-time', [EmployeeController::class, 'updateAttendanceTime'])
            ->name('attendance.updateTime');

        Route::post('/attendance/manual', [AttendanceController::class, 'manual'])
            ->name('attendance.manual');

        Route::get('/master-client', [ClientController::class, 'index'])
            ->name('clients.index');
        Route::resource('clients', ClientController::class);

        Route::get('/attendance-monitoring', [AttendanceController::class, 'monitoring'])
            ->name('attendance.monitoring');
        Route::get('/attendance-monitoring/export', [AttendanceController::class, 'exportMonitoring'])
            ->name('attendance.monitoring.export');

    });

    Route::middleware('role:admin,talent acquisition')->group(function () {
        Route::resource('career', JobController::class);
        Route::get('/career/{id}/applicants', [JobController::class, 'applicants'])
            ->name('career.applicants');

        Route::post('/career/application/{id}', [JobController::class, 'updateApplication'])
            ->name('career.application.update');
    });

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])
        ->name('attendance.my');

    Route::get('/my-attendance/export', [AttendanceController::class, 'export'])
        ->name('attendance.export');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::get('/career-public', [PublicController::class, 'index'])->name('public.index');
Route::get('/career-public/{slug}', [PublicController::class, 'show'])->name('public.show');
Route::post('/career-public/apply', [PublicController::class, 'apply'])->name('public.apply');