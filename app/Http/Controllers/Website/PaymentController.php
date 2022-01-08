<?php

namespace App\Http\Controllers\Website;

use App\Models\Payment;
use App\Models\Merchant;
use App\Enums\PaymentStatus;
use App\Enums\ReferenceType;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\Website\PaymentFilter;
use App\Http\Resources\MainPaginatedCollection;
use App\Http\Resources\Website\PaymentResource;
use App\Http\Requests\Website\PaymentStoreRequest;
use App\Http\Requests\Website\PaymentUpdateRequest;

class PaymentController extends Controller
{
    /** \App\Models\Merchant */
    protected $merchant;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PaymentFilter $filters)
    {
        //$payments = Payment::filter($filters)->byMerchant($merchant->id)->latest()->paginate(per_page());
        $payments = $this->merchant()->payments()
            ->filter($filters)
            ->latest()
            ->paginate(per_page());
        //$payments = Payment::filter($filters)->paginate(per_page());

        return new MainPaginatedCollection(
            PaymentResource::collection($payments),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentStoreRequest $request)
    {
        $method = $request->input('method');
        $price = $request->product->usd_price;
        $customer = PaymentService::getUserCustomerId(Auth::user(), $method);
        $paymentService = PaymentService::init($method, $request->account_id);

        $order = $paymentService->createOrder($customer, $price);

        //$this->product->update([
        //    'quantity' => $request->product->quantity - 1,
        //]);

        $payment = Auth::user()->payments()->create(array_merge(
            $request->validated(),
            [
                'merchant_id' => $request->product->merchant_id,
                'amount' => $price,
                'reference_id' => $order->id,
                'payment_method_id' => PaymentMethod::byKey($method),
                //'status' => $paymentService->checkPaymentStatus($order->status),
                'status' => $order->status,
            ],
        ));

        //$reference = ReferenceType::$methods[$request->input('method')][ReferenceType::ORDER_ID];
        //$payment->createReference($reference, $order->id);
        //$payment->createReference(ReferenceType::CUSTOMER_ID, $customer);
        $payment->createReference(ReferenceType::MERCHANT_ID, $request->product->getAccountIdByMethod($method));
        $payment->createReference(ReferenceType::ORDER_ID, $order->id);

        return response([
            'client_secret' => optional($order)->client_secret,
            'payment_id' => $order->id,
            'payment' => new PaymentResource($payment),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        return response([
            'payment' => new PaymentResource($payment),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentUpdateRequest $request, Payment $payment)
    {
        $method = $payment->method->key;
        $accountId = $payment->getReferenceIdByType('merchant_id');
        $paymentService = PaymentService::init($method, $accountId);
        $orderId = $payment->getReferenceIdByType('order_id');
        $order = $paymentService->showOrder($orderId);

        //if ($request->status === PaymentStatus::CANCELED) {
        //}

        $payment->update([
            'status' => $paymentService->checkPaymentStatus($order->status),
        ]);

        return response([
            'order' => $order,
            'payment' => new PaymentResource($payment->fresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
