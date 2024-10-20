<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    $this->artisan('migrate:fresh');
    $this->artisan('db:seed');
});

it('can request a password reset', function () {
    $user = User::factory()->create([
        'email' => 'librarian@example.com',
    ]);

    $response = $this->postJson('/api/password/email', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'We have emailed your password reset link!']);
});

it('fails to request a password reset for a non-existing email', function () {
    $response = $this->postJson('/api/password/email', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

it('can reset password with valid token', function () {
    // Create a user
    $user = User::factory()->create([
        'email' => 'librarian@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    // Request a password reset token
    $token = Password::broker()->createToken($user);

    // Reset the password
    $newPassword = 'newpassword123';

    $response = $this->postJson('/api/password/reset', [
        'email' => $user->email,
        'token' => $token,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Your password has been reset!']);

    // Verify that the password was actually reset
    $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
});

it('fails to reset password with an invalid token', function () {
    // Create a user
    $user = User::factory()->create([
        'email' => 'librarian@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    // Attempt to reset with an invalid token
    $newPassword = 'newpassword123';

    $response = $this->postJson('/api/password/reset', [
        'email' => $user->email,
        'token' => 'invalidtoken',
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});
