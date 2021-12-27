<?php

namespace App\Services\Payment;

use Stripe\OAuth;
use Stripe\Stripe;
use Stripe\StripeClient;
use App\Enums\MerchantStatus;
use App\Enums\PaymentStatus;
use Exception;
use Stripe\Exception\OAuth\InvalidGrantException;

class CreditCard
{
    protected $method = 'credit_card';

    protected $gateway = 'stripe';

    protected $accountId;

    public function __construct($accountId = null)
    {
        $this->accountId = $accountId;

        $options = [];

        if ($this->accountId) {
            $options['stripe_account'] = $accountId;
        }

        Stripe::setApiKey(config('payment.gateways.stripe.secret'));

        Stripe::setApiVersion('2020-08-27');

        $this->client = new StripeClient(config('payment.gateways.stripe.secret'), $options);
    }

    public function resource($data)
    {
        $data['method'] = $this->method;
        $data['gateway'] = $this->gateway;

        return (object) $data;
    }

    public static function checkMerchantStatus($account)
    {
        $connected = $account->charges_enabled && !count($account->currently_due);

        if ($connected) {
            return MerchantStatus::CONNECTED;
        }

        return MerchantStatus::PENDING;
    }

    public function checkPaymentStatus($status)
    {
        $statuses = [
            'requires_capture' => PaymentStatus::PENDING,
            'processing' => PaymentStatus::PENDING,
            'requires_action' => PaymentStatus::PENDING,
            'requires_payment_method' => PaymentStatus::PENDING,
            'requires_confirmation' => PaymentStatus::PENDING,
            'succeeded' => PaymentStatus::SUCCEEDED,
            'canceled' => PaymentStatus::CANCELED,
            'failed' => PaymentStatus::FAILED,
            'refunded' => PaymentStatus::REFUNDED,
        ];

        if (!array_key_exists($status, $statuses)) {
            throw new Exception('status isn\'t handled for stripe');
        }

        return $statuses[$status];
    }

    public function createCardToken($cvc)
    {
        $token = $this->clien->tokens->create([
            'cvc_update' => ['cvc' => $cvc],
        ]);

        return $this->resource([
            'id' => $token->id,
        ]);
    }

    public function createCard(string $customer, array $data)
    {
        return [
            $customer,
            ['source' => $data['token']],
        ];

        $card = $this->client->customers->createSource(
            $customer,
            ['source' => $data['token']],
        );

        return $this->resource([
            'id' => $card->id,
        ]);
    }

    public function getCustomer(string $customer)
    {
        $customer = $this->client->customers->retrieve($customer);

        return $this->resource([
            'id' => $customer->id,
            'default_source' => $customer->default_source,
            'invoice_prefix' => $customer->invoice_prefix,
            'balance' => $customer->balance,
            'currency' => $customer->currency,
            'created' => $customer->created,
        ]);
    }

    public function getCustomerByEmail(string $email)
    {
        $customer = $this->client->customers->all(['limit' => 1, 'email' => $email]);

        if (!($customer && $customer->data && count($customer->data))) {
            return null;
        }

        return $customer->data[0];
    }

    public function createCustomer($data)
    {
        $customer = $this->getCustomerByEmail($data['email']) ?: $this->client->customers->create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $this->resource([
            'id' => $customer->id,
            //'object' => $customer,
        ]);
    }

    public function createMerchantLink($email, $name, $uri, $businessName)
    {
        $clientId = config('payment.gateways.stripe.client_id');

        $refreshUrl = client_url($uri);
        $returnUrl = client_url($uri);

        $connectUrl = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id={$clientId}&scope=read_write&redirect_uri={$returnUrl}";

        return $this->resource([
            'url' => $connectUrl,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
        ]);
    }

    public function getMerchant($accountId)
    {
        $account = $this->client->accounts->retrieve($accountId);

        return $this->resource([
            'id' => $account->id,
            //'capabilities' => $account->capabilities,
            'charges_enabled' => $account->charges_enabled,
            'default_currency' => $account->default_currency,
            'details_submitted' => $account->details_submitted,
            'currently_due' => $account->requirements->currently_due,
            'eventually_due' => $account->requirements->eventually_due,
            'disabled_reason' => $account->requirements->disabled_reason,
            'object' => $account,
        ]);
    }

    public function authorizeMerchant($code)
    {
        try {
            $oauth = OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);
        } catch (InvalidGrantException $th) {
            return $this->resource([
                'authorized' => false,
            ]);
        }

        return $this->resource([
            'account_id' => $oauth->stripe_user_id,
            'authorized' => true,
        ]);
    }

    public function attachPaymentMethod(string $customerId, string $id)
    {
        $paymentMethod = $this->client->paymentMethods->attach(
            $id,
            ['customer' => $customerId],
        );

        return $this->resource([
            'id' => $paymentMethod->id,
        ]);
    }

    public function showOrder(string $orderId)
    {
        $order = $this->client->paymentIntents->retrieve($orderId);

        $status = $this->checkPaymentStatus($order['status']);

        return $this->resource([
            'id' => $order['id'],
            'status' => $status,
        ]);
    }

    public function createOrder(string $custormerId, int $amount, string $currency = 'USD')
    {
        $paymentIntent = $this->client->paymentIntents->create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'on_behalf_of' => $this->accountId,
            //'payment_method' => $paymentMethod,
            'customer' => $custormerId,
            'payment_method_types' => ['card'],
            //'transfer_data' => [
            //    'destination' => $this->accountId,
            //],
        ]);

        return $this->resource([
            'id' => $paymentIntent->id,
            'paid_amount' => $paymentIntent->amount_received,
            'status' => $paymentIntent->status,
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }

    public function createSubscription(string $priceId, string $customer, string $currency = 'USD')
    {
        $subscription = $this->client->subscriptions->create([
            'payment_behavior' => 'allow_incomplete',
            'customer' => $customer,
            'items' => [
                ['price' => $priceId],
            ],
            // TODO: add
            //'transfer_data' => [
            //    'destination' => $this->accountId,
            //],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        return $this->resource([
            'id' => $subscription->id,
            'paid_amount' => $subscription->unit_amount,
            'type' => $subscription->type,
        ]);
    }

    public function cancelSubscription(string $subscriptionId)
    {
        $subscription = $this->client->subscriptions->cancel($subscriptionId);

        return $this->resource([
            'id' => $subscription->id,
            'paid_amount' => $subscription->unit_amount,
            'type' => $subscription->type,
        ]);
    }

    public function createPrice(int $amount, ?string $interval, string $currency)
    {
        $price = $this->client->prices->create([
            'unit_amount' => $amount * 100,
            'currency' => $currency,
            'recurring' => $interval ? ['interval' => $interval] : [],
            'product_data' => [
                'name' => $amount . $currency . ' subscription',
            ],
        ]);

        return $this->resource([
            'id' => $price->id,
            'valid' => $price->active,
            'type' => $price->type,
        ]);
    }

    public function getPrice(string $priceId)
    {
        $price = $this->client->prices->retrieve($priceId, [
            'expand' => [
                'product',
                //'metadata',
            ],
        ]);

        return $this->resource([
            'id' => $price->id,
            'raw' => $price,
        ]);
    }

    public function createPlan(int $amount, string $currency)
    {
        $product = $this->createProduct();

        $plan = $this->client->plans->create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'interval' => 'month',
            'product' => $product->id,
        ]);

        //$price = $this->client->prices->create([
        //    'unit_amount' => $amount * 100,
        //    'currency' => $currency,
        //    'recurring' => ['interval' => 'month'],
        //    'product_data' => [
        //        'name' => $amount . $currency . ' subscription',
        //    ],
        //]);

        return $this->resource([
            'id' => $plan->id,
        ]);
    }

    public function createProduct()
    {
        $product = $this->client->products->create([
          'name' => 'subscription',
        ]);

        return $this->resource([
            'id' => $product->id,
        ]);
    }

    public function getInvoice($invoiceId)
    {
        $invoice = $this->client->invoices->retrieve($invoiceId);

        return $this->resource([
            'id' => $invoice->id,
            'payment_id' => $invoice->charge,
            'amount_due' => $invoice->amount_due,
            'amount_paid' => $invoice->amount_paid,
            'amount_remaining' => $invoice->amount_remaining,
            'currency' => $invoice->currency,
            'customer_id' => $invoice->customer,
        ]);
    }

    public function validateStatus($merchant)
    {
        $currentlyDue = $merchant->currently_due && count($merchant->currently_due);

        return !$currentlyDue && !$merchant->disabled_reason && $merchant->charges_enabled;
    }
}
