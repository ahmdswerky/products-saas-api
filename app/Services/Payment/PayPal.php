<?php

namespace App\Services\Payment;

use App\Helpers\PayPalSDK;
use App\Enums\PaymentStatus;
use App\Enums\MerchantStatus;
use Exception;

class PayPal
{
    protected $method = 'paypal';

    protected $gateway = 'paypal';

    public function __construct($accountId = null)
    {
        $this->client = new PayPalSDK(
            config('payment.gateways.paypal.client_id'),
            config('payment.gateways.paypal.secret'),
            config('payment.gateways.paypal.bn_code'),
            $accountId,
        );
    }

    public function resource($data)
    {
        $data['method'] = $this->method;
        $data['gateway'] = $this->gateway;

        return (object) $data;
    }

    public static function checkMerchantStatus($account)
    {
        $connected = $account->is_able_to_accept_payments && $account->primary_email_confirmed;

        if ($connected) {
            return MerchantStatus::CONNECTED;
        }

        return MerchantStatus::PENDING;
    }

    public function checkPaymentStatus($status)
    {
        $statuses = [
            'PENDING' => PaymentStatus::PENDING,
            'CREATED' => PaymentStatus::PENDING,
            'SAVED' => PaymentStatus::PENDING,
            'APPROVED' => PaymentStatus::SUCCEEDED,
            'PAYER_ACTION_REQUIRED' => PaymentStatus::PENDING,
            'COMPLETED' => PaymentStatus::SUCCEEDED,
            'VOIDED' => PaymentStatus::FAILED,
            'DECLINED' => PaymentStatus::FAILED,
            'REFUNDED' => PaymentStatus::REFUNDED,
            'FAILED' => PaymentStatus::FAILED,
        ];

        if (!array_key_exists($status, $statuses)) {
            throw new Exception('status isn\'t handled for paypal');
        }

        return $statuses[$status];
    }

    public function createCustomer()
    {
        return $this->resource([
            'id' => '',
        ]);
    }

    public function createMerchantLink($email, $name, $uri, $businessName)
    {
        $url = $this->client->createMerchantLink($email, $name, $businessName, $uri);

        return $this->resource([
            'url' => $url,
        ]);
    }

    public function getMerchant($accountId)
    {
        $merchantPartnerId = config('payment.gateways.paypal.partner_id');
        $account = $this->client->getMerchant($merchantPartnerId, $accountId);

        return $this->resource([
            'id' => $account['id'],
            'products' => $account['products'],
            'is_able_to_accept_payments' => $account['is_able_to_accept_payments'],
            'primary_email_confirmed' => $account['primary_email_confirmed'],
        ]);
    }

    public function showOrder(string $orderId)
    {
        $order = $this->client->showOrder($orderId);

        $status = $this->checkPaymentStatus($order['status']);

        return $this->resource([
            'id' => $order['id'],
            'status' => $status,
        ]);
    }

    public function createOrder(?string $custoemerId, int $amount, string $currency = 'USD')
    {
        $order = $this->client->createOrder($amount, $currency);

        return $this->resource([
            'id' => $order['id'],
            'status' => $order['status'],
            'paid_amount' => $amount,
        ]);
    }
}
