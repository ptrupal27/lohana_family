<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FamilyMemberController;
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
        Route::apiResource('members.family-members', FamilyMemberController::class)
            ->only(['store', 'show', 'update', 'destroy'])
            ->parameters(['family-members' => 'familyMember']);
    });
});
