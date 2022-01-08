<?php

use App\Models\User;
use App\Models\Merchant;

it('creates a merchant for each user', function () {
    $merchantsCount = Merchant::count();
    $usersCount = User::count();

    expect($merchantsCount)->toEqual($usersCount);
});
