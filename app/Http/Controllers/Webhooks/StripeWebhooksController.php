<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\StripeWebhooks\ProcessStripeWebhookJob;
use Spatie\StripeWebhooks\StripeSignatureValidator;

class StripeWebhooksController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $webhookConfig = new WebhookConfig([
            'name' => 'stripe',
            'signing_secret' => config('stripe-webhooks.signing_secret'),
            'signature_header_name' => 'Stripe-Signature',
            'signature_validator' => StripeSignatureValidator::class,
            'webhook_profile' => config('stripe-webhooks.profile'),
            'webhook_model' => config('stripe-webhooks.model'),
            'process_webhook_job' => ProcessStripeWebhookJob::class,
        ]);

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }
}
