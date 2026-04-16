<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('user');

        Route::apiResource('members', MemberController::class);
        Route::prefix('members/{member}/family-members')->group(function () {
            Route::post('/', [MemberController::class, 'storeFamilyMember'])->name('members.family-members.store');
            Route::get('/{familyMember}', [MemberController::class, 'showFamilyMember'])->name('members.family-members.show');
            Route::put('/{familyMember}', [MemberController::class, 'updateFamilyMember'])->name('members.family-members.update');
            Route::delete('/{familyMember}', [MemberController::class, 'destroyFamilyMember'])->name('members.family-members.destroy');
        });
    });
});
