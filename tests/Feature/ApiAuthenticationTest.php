<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('users can log in and receive an api token', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Pest Test Device',
    ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('user.email', $user->email);

    expect($response->json('token'))->toBeString()->not->toBeEmpty();
});

test('authenticated users can call the members api', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $loginResponse = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Members API Test Device',
    ]);

    $token = $loginResponse->json('token');

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/members')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
});

test('users can log out and revoke their current api token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Logout Test Device')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/logout')
        ->assertSuccessful()
        ->assertJsonPath('message', 'Logout successful.');

    expect($user->fresh()->tokens)->toHaveCount(0);
});
