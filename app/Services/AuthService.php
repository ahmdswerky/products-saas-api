<?php

namespace App\Services;

use App\Models\User;
use App\Models\Verification;

class AuthService
{
    public function generateToken(User $user)
    {
        $token = $user->createToken('main');

        if ($token) {
            return $token->accessToken;
        }

        return null;
    }

    public static function revokeResets($email)
    {
        $resets = Verification::byUsername($email)
            ->byType('passsword_reset')
            ->get();

        $resets->each->delete();
    }
}
