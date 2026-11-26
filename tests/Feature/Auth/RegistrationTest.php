<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});

test('registration ignores is_admin mass assignment attempt', function () {
    $response = $this->post('/register', [
        'name' => 'Attacker User',
        'email' => 'attacker@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'is_admin' => true,
    ]);

    $this->assertAuthenticated();
    $user = \App\Models\User::where('email', 'attacker@example.com')->firstOrFail();
    expect($user->is_admin)->toBeFalse();
});
