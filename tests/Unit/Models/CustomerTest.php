<?php

use App\Models\User;
use App\Models\PaymentGateway;

it('each user have customers', function () {
    $gatewaysCount = PaymentGateway::count();
    $user = User::first();

    expect($user->customers)->toHaveCount($gatewaysCount);
});
