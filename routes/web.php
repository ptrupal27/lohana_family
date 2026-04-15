<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('members', MemberController::class);

    Route::post('members/{member}/family-members', [FamilyMemberController::class, 'store'])->name('family-members.store');
    Route::get('members/{member}/family-members/{familyMember}/edit', [FamilyMemberController::class, 'edit'])->name('family-members.edit');
    Route::put('members/{member}/family-members/{familyMember}', [FamilyMemberController::class, 'update'])->name('family-members.update');
    Route::delete('members/{member}/family-members/{familyMember}', [FamilyMemberController::class, 'destroy'])->name('family-members.destroy');
});
