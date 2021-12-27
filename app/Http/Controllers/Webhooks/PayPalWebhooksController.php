<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayPalWebhooksController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $eventType = strtolower($request->event_type);

        $job = config('paypal-webhooks.jobs')[$eventType] ?? '';

        if ($job && class_exists($job)) {
            dispatch(new $job($request));
        }

        return response([
            'message' => 'ok',
        ]);
    }
}
