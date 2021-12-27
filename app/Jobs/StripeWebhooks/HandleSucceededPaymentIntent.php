<?php

namespace App\Jobs\StripeWebhooks;

use App\Enums\PaymentStatus;
use App\Enums\ReferenceType;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\WebhookClient\Models\WebhookCall;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class HandleSucceededPaymentIntent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Spatie\WebhookClient\Models\WebhookCall */
    public $webhookCall;

    /** @var object */
    public $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;

        $this->payload = array_to_object($this->webhookCall->payload)->data->object;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payment = Payment::byReference(ReferenceType::ORDER_ID, $this->payload->id);
        //$paymentIntentId = $this->payload->id;
        //$charge = ((array) $this->payload->charges->data)[0];
        $charge = collect($this->payload->charges->data)->first();

        $chargeId = $charge->id;
        //$status = $charge->status;
        $paymentMethod = $charge->payment_method;
        //$card = $charge->payment_method_details->card;
        //$cardBrand = $card->brand;
        //$last4 = $card->last4;
        //$amount = $this->payload->amount_received;

        info('product [' . $payment->product_id . '] $' . $payment->usd_amount . ' -> ' . );

        $payment->update([
            'status' => PaymentStatus::SUCCEEDED,
        ]);

        $payment->createMultipleReferences([
            ReferenceType::CHARGE => $chargeId,
            ReferenceType::PAYMENT_METHOD => $paymentMethod,
        ]);
    }
}
