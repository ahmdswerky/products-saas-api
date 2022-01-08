<?php

use App\Models\User;

it('a test user is created', function () {
    $user = User::first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Customer');
    expect($user->email)->toBe('test@test.com');
});
