<?php

namespace App\Services;

use App\Models\User;
use App\Enums\MerchantStatus;
use App\Services\Payment\PayPal;
use App\Services\Payment\CreditCard;

class PaymentService
{
    public static $defaultMethods = [
        'stripe' => 'credit_card',
        'paypal' => 'paypal',
    ];

    public static $gateways = [
        'credit_card' => 'stripe',
        'paypal' => 'paypal',
    ];

    protected static $methods = [
        'credit_card' => CreditCard::class,
        'paypal' => PayPal::class,
    ];

    public $method = null;

    protected $accountId;

    protected $target;

    /**
     * @param PaymentContract|string $method
     * @return void
     */
    public function __construct($method = null, $accountId = null)
    {
        if (gettype($method) === 'string') {
            $method = new self::$methods[$method]($accountId);
        }

        $this->method = $method;
    }

    /**
     * initalize payment service
     *
     * @param stroing $method
     * @param stroing $accountId
     * @return self
     */
    public static function init($method = null, $accountId = null): self
    {
        return new self($method, $accountId);
    }

    public static function defaultMethod($gateway)
    {
        return self::$defaultMethods[$gateway] ?? null;
    }

    public static function gateway($method)
    {
        return self::$gateways[$method] ?? null;
    }

    public function getCustomerByEmail(string $email)
    {
        return $this->method->getCustomerByEmail($email);
    }

    public function getCustomer(string $customer)
    {
        return $this->method->getCustomer($customer);
    }

    public function checkMerchantStatus($account)
    {
        return $this->method->checkMerchantStatus($account);
    }

    public function checkPaymentStatus($status)
    {
        return $this->method->checkPaymentStatus($status);
    }

    public function createCard(string $customer, array $data)
    {
        return $this->method->createCard($customer, $data);
    }

    public function createCustomer(array $data)
    {
        return $this->method->createCustomer($data);
    }

    public static function createCustomers($customer): array
    {
        $accounts = [];

        collect(self::$methods)->keys()->map(function ($key) use (&$accounts, $customer) {
            $payment = self::init($key);
            $accounts[] = $payment->createCustomer($customer);
        });

        return $accounts;
    }

    public function createMerchantLink(string $email, string $name, string $uri, string $businessName)
    {
        return $this->method->createMerchantLink($email, $name, $uri, $businessName);
    }

    public function createMerchant($merchant)
    {
        return $this->method->createMerchant($merchant);
    }

    public function createPrice(int $amount, ?string $interval = null, string $currency = 'usd')
    {
        return $this->method->createPrice($amount, $interval, $currency);
    }

    public static function getUserCustomer(User $user, $method)
    {
        return $user->customers()->where(function ($query) use ($method) {
            $query->whereHas('gateway', function ($query) use ($method) {
                $query->whereHas('methods', function ($query) use ($method) {
                    $query->where('key', $method);
                });
            });
        })->first();
    }

    public static function getUserCustomerId(User $user, $method)
    {
        $customer = self::getUserCustomer($user, $method);

        return optional($customer)->reference_id;
    }

    public function getMerchant($accountId): object
    {
        return $this->method->getMerchant($accountId);
    }

    public function authorizeMerchant($code)
    {
        return $this->method->authorizeMerchant($code);
    }

    public function showOrder(string $orderId): object
    {
        return $this->method->showOrder($orderId);
    }

    public function createOrder(?string $customerId, int $amount, string $currency = 'USD'): object
    {
        return $this->method->createOrder($customerId, $amount, $currency);
    }

    public function attachPaymentMethod(string $customerId, string $id): object
    {
        return $this->method->attachPaymentMethod($customerId, $id);
    }
}
