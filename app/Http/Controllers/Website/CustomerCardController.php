<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Website\CustomerCardRequest;

class CustomerCardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CustomerCardRequest $request)
    {
        $customerId = PaymentService::getUserCustomerId(Auth::user(), $request->input('method'));

        $payment = PaymentService::init($request->input('method'));

        $payment->createCard($customerId, $request->only('token'));

        return response([], 204);
    }
}
