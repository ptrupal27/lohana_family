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
    Route::get('members/print-labels', [MemberController::class, 'printLabels'])->name('members.print.labels');
    Route::get('members/{member}/print', [MemberController::class, 'printSingle'])->name('members.print.single');
    Route::get('members/export/5excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');

    Route::resource('members', MemberController::class);

    Route::get('members/{member}/family-members/{familyMember}/edit', [MemberController::class, 'editFamilyMember'])->name('family-members.edit');

    // API Routes moved from api.php
    Route::prefix('api')->name('api.')->group(function () {
        Route::apiResource('members', MemberController::class);
        Route::prefix('members/{member}/family-members')->group(function () {
            Route::post('/', [MemberController::class, 'storeFamilyMember'])->name('members.family-members.store');
            Route::get('/{familyMember}', [MemberController::class, 'showFamilyMember'])->name('members.family-members.show');
            Route::put('/{familyMember}', [MemberController::class, 'updateFamilyMember'])->name('members.family-members.update');
            Route::delete('/{familyMember}', [MemberController::class, 'destroyFamilyMember'])->name('members.family-members.destroy');
        });
    });
});
