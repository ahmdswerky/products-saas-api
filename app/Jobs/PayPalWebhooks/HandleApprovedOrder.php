<?php

namespace App\Jobs\PayPalWebhooks;

use App\Models\Payment;
use App\Enums\ReferenceType;
use Illuminate\Bus\Queueable;
use App\Services\PaymentService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class HandleApprovedOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var object */
    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->payload = array_to_object($request->resource);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payment = Payment::byReference(ReferenceType::ORDER_ID, $this->payload->id);
        $purchasedUnit = collect($this->payload->purchase_units)->first();
        $merchantId = $purchasedUnit->payee->merchant_id;
        $customerId = $this->payload->payer->payer_id;
        $customerEmail = $this->payload->payer->email_address;

        $paymentService = PaymentService::init('paypal', $merchantId);
        $order = $paymentService->showOrder($this->payload->id);

        $payment->update([
            'status' => $order->status,
        ]);

        $payment->createMultipleReferences([
            ReferenceType::CUSTOMER_ID => $customerId,
            ReferenceType::CUSTOMER_EMAIL => $customerEmail,
        ]);
    }
}
