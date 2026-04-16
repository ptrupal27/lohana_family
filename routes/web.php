<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('members/print', [MemberController::class, 'printAll'])->name('members.print.all');
    Route::get('members/{member}/print', [MemberController::class, 'printSingle'])->name('members.print.single');
    Route::get('members/export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');

    Route::resource('members', MemberController::class);

    Route::get('members/{member}/family-members/{familyMember}/edit', [MemberController::class, 'editFamilyMember'])->name('family-members.edit');
});
