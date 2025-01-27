<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
// });

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [DashboardController::class, 'users'])->name('admin.users');
    Route::get('/user/{id}', [DashboardController::class, 'user'])->name('admin.user');
    Route::get('/update-user/{id}', [DashboardController::class, 'editUser'])->name('admin.update-user');
    Route::delete('/delete-user/{id}', [DashboardController::class, 'deleteUser'])->name('admin.delete-user');
    Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.update');
    Route::get('/projects', [DashboardController::class, 'projects'])->name('admin.projects');
    Route::get('/project/{id}', [DashboardController::class, 'project'])->name('admin.project');
    Route::put('/project/{id}', [DashboardController::class, 'updateProject'])->name('admin.project-update');
    Route::get('/update-project/{id}', [DashboardController::class, 'editProject'])->name('admin.update-project');
    Route::get('/simulations', [DashboardController::class, 'simulations'])->name('admin.simulations');
    Route::get('/simulation/{id}', [DashboardController::class, 'simulation'])->name('admin.simulation');
    Route::put('/user/{id}/enable', [DashboardController::class, 'toggleEnable'])->name('admin.toggle-enable');
});

require __DIR__.'/auth.php';
