<?php

return [
    /*
     * You can define the job that should be run when a certain webhook hits your application
     * here. The key is the name of the Stripe event type with the `.` replaced by a `_`.
     *
     * You can find a list of Stripe webhook types here:
     * https://paypal.com/docs/api#event_types.
     */
    'jobs' => [
        'checkout.order.approved' => \App\Jobs\PayPalWebhooks\HandleApprovedOrder::class,
    ],
];
