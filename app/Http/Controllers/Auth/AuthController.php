<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\SigninRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Resources\Website\UserResource;
use App\Http\Requests\Website\UserUpdateRequest;

class AuthController extends Controller
{
    public function signin(SigninRequest $request)
    {
        $user = User::byEmail($request->email)->first();

        $token = $user->createToken('main');

        return response([
            'token' => $token->accessToken,
            'user' => new UserResource($user),
        ]);
    }

    public function signup(SignupRequest $request)
    {
        $user = User::create($request->validated());

        $avatar = generate_letter_image($user->name[0]);

        $user->addMedia($avatar, 'photo', true, [
            'name' => 'avatar',
        ], true);

        $customers = PaymentService::createCustomers([
            'name' => $user->name,
            'email' => $user->email,
        ]);

        collect($customers)->map(function ($customer) use ($user) {
            $user->customers()->create([
                'public_id' => Str::random(20),
                'reference_id' => $customer->id,
                'payment_gateway_id' => PaymentGateway::byKey($customer->gateway),
            ]);
        });

        $merchant = $user->merchants()->create([
            'public_id' => Str::random(20),
            'api_key' => Str::random(15),
            'api_secret' => Str::random(30),
            'title' => 'The Store',
            'payment_gateway_id' => PaymentGateway::byKey('stripe'),
        ]);

        $avatar = 'https://cdn-icons-png.flaticon.com/512/4483/4483129.png';

        $merchant->addMedia($avatar, 'photo', true, [
            'name' => 'avatar',
        ], true);

        $token = $user->createToken('main');

        return response([
            'token' => $token->accessToken,
            'user' => new UserResource($user),
        ]);
    }

    public function signout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response([
            'message' => __('messages.logged_out'),
        ]);
    }

    public function user()
    {
        $user = Auth::user();

        return response([
            'user' => new UserResource($user),
        ]);
    }

    public function update(UserUpdateRequest $request)
    {
        $user = Auth::user();

        $user->update($request->validated());

        return response([
            'user' => new UserResource($user->fresh()),
        ]);
    }
}
