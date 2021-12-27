<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Website\AttachPaymentMethodRequest;

class AttachPaymentMethodController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(AttachPaymentMethodRequest $request)
    {
        $method = $request->input('method');
        $customerId = PaymentService::getUserCustomerId(Auth::user(), $method);
        $paymentService = PaymentService::init($method);

        $paymentService->attachPaymentMethod($customerId, $request->input('method_id'));

        return response([], 204);
    }
}
