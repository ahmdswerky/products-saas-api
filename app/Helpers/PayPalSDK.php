<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class PayPalSDK
{
    // endpoints
    const OBTAIN_TOKEN_ENDPOINT = '/v1/oauth2/token';

    const GENERATE_TOKEN_ENDPOINT = '/v1/identity/generate-token';

    const PARTNER_REFERAL_ENDPOINT = '/v2/customer/partner-referrals';

    const SHOW_MERCHANT_ENDPOINT = '/v1/customer/partners/%s/merchant-integrations/%s';

    const CREATE_ORDER_ENDPOINT = '/v2/checkout/orders';

    const SHOW_ORDER_ENDPOINT = '/v2/checkout/orders/%s';

    const AUTHORIZE_ORDER_ENDPOINT = '/v2/checkout/orders/%s/authorize';

    /** @var string */
    protected $tokenType = 'Basic';

    /** @var string */
    protected $accountId;

    /** @var string */
    protected $token;

    /** @var string */
    protected $clientId;

    /** @var string */
    protected $clientSecret;

    /** @var string */
    protected $bnCode;

    public function __construct($clientId, $clientSecret, $bnCode = null, $accountId = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->bnCode = $bnCode;
        $this->accountId = $accountId;

        $this->generateToken();
    }

    public function url($endpoint)
    {
        if (!Str::startsWith($endpoint, '/')) {
            $endpoint = '/' . $endpoint;
        }

        return url(config('payment.gateways.paypal.api_url') . $endpoint);
    }

    public function http($skipValidation = false): PendingRequest
    {
        //$http = Http::withHeaders([
        //    'PayPal-Partner-Attribution-Id' => $this->bnCode,
        //]);
        $http = Http::withOptions([
            //'debug' => true,
        ]);

        if ($this->tokenType === 'Basic') {
            $http = $http->withBasicAuth($this->clientId, $this->clientSecret);
        } elseif ($this->tokenType === 'Bearer' && $this->token) {
            $http = $http->withToken($this->token);
        } elseif (!$skipValidation) {
            throw new Exception('No authentication credentials specified for paypal.');
        }

        if ($this->accountId) {
            $http = $http->withHeaders([
                'PayPal-Auth-Assertion' => $this->buildAssertion($this->clientId, $this->accountId),
            ]);
        }

        //if ($this->token) {
        //    $http = $http->withToken($this->token);
        //} else {
        //    $http = $http->withBasicAuth($clientId, $clientSecret);
        //}

        return $http;
    }

    public function buildAssertion($clientId, $accountId)
    {
        $parts = [
            ['alg' => 'none'],
            [
                'iss' => $clientId,
                'payer_id' => $accountId,
            ],
            "",
        ];

        $result = collect($parts)->map(function ($part) {
            return base64_encode(
                gettype($part) === 'string' ? $part : json_encode($part)
            );
        })->toArray();

        return implode('.', $result);
    }

    public function generateToken()
    {
        //dd(
        //    $this->http(true)->withOptions([
        //        //'debug' => true
        //    ])->asForm()->post($this->url(self::OBTAIN_TOKEN_ENDPOINT), [
        //        'grant_type' => 'client_credentials',
        //    ])->object()
        //);

        $response = $this->http(true)->withOptions([
            //'debug' => true
        ])->asForm()->post($this->url(self::OBTAIN_TOKEN_ENDPOINT), [
            'grant_type' => 'client_credentials',
        ])->object();

        //$this->setToken($response->access_token, $response->token_type);
        $this->setToken($response->access_token, 'Bearer');

        //return [
        //    'ab' => $this->buildAssertion($this->clientId, $this->accountId),
        //    'a' => $response
        //];

        //$response = $this->http()->send('POST', $this->url(self::GENERATE_TOKEN_ENDPOINT))->object();

        //$this->setToken($response->client_token);
    }

    public function setToken($token, $type)
    {
        $this->tokenType = $type;

        $this->token = $token;
    }

    public function createMerchantLink($email, $name, $businessName, $uri)
    {
        $parts = explode(' ', $name);
        $firstName = $parts[0];
        $lastName = $parts[1] ?? '';

        $refreshUrl = client_url($uri);
        $returnUrl = client_url($uri);

        $payload = [
            'individual_owners' => [
                [
                    'names' => [
                        [
                            'given_name' => $firstName,
                            'surname' => $lastName,
                            'full_name' => $name,
                            'type' => 'LEGAL'
                        ],
                    ],
                    'type' => 'PRIMARY',
                ]
            ],
            'business_entity' => [
                'business_type' => [
                        'type' => 'INDIVIDUAL',
                        'subtype' => 'ASSO_TYPE_INCORPORATED',
                    ],
                ],
                'names' => [
                    [
                        'business_name' => $businessName,
                        'type' => 'LEGAL_NAME',
                    ],
                ],
                'emails' => [
                    [
                        'type' => 'CUSTOMER_SERVICE',
                        'email' => $email,
                    ],
                ],
                'beneficial_owners' => [
                    'individual_beneficial_owners' => [
                        [
                            'names' => [
                                [
                                    'given_name' => $firstName,
                                    'surname' => $lastName,
                                    'full_name' => $name,
                                    'type' => 'LEGAL'
                                ],
                            ],
                            'percentage_of_ownership' => '100'
                        ],
                    ],
                    'business_beneficial_owners' => [
                        [
                            'business_type' => [
                                'type' => 'INDIVIDUAL',
                                'subtype' => 'ASSO_TYPE_INCORPORATED'
                            ],
                            'names' => [
                                [
                                    'business_name' => $businessName,
                                    'type' => 'LEGAL_NAME',
                                ],
                            ],
                            'emails' => [
                                [
                                    'type' => 'CUSTOMER_SERVICE',
                                    'email' => $email,
                                ],
                            ],
                            'percentage_of_ownership' => '100',
                        ],
                    ],
                ],
                'office_bearers' => [
                    [
                        'names' => [
                            [
                                'given_name' => $firstName,
                                'surname' => $lastName,
                                'full_name' => $name,
                                'type' => 'LEGAL',
                            ],
                        ],
                        'role' => 'DIRECTOR',
                    ],
                ],
                'purpose_code' => 'P0104',
                'email' => $email,
                'preferred_language_code' => 'en-US',
                'tracking_id' => 'testenterprices123122',
                'partner_config_override' => [
                // 'partner_logo_url' => 'https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg',
                'return_url' => $returnUrl,
                // 'return_url_description' => 'the url to return the merchant after the paypal onboarding process.',
                'action_renewal_url' => 'https://google.com',
                'show_add_credit_card' => true,
            ],
            'operations' => [
                [
                    'operation' => 'API_INTEGRATION',
                    'api_integration_preference' => [
                        'classic_api_integration' => [
                            //'integration_method' => 'PAYPAL',
                            'integration_type' => 'THIRD_PARTY',
                            "third_party_details" => [
                                "permissions" => [
                                    "EXPRESS_CHECKOUT",
                                    "DIRECT_PAYMENT",
                                    "ACCOUNT_BALANCE",
                                    "TRANSACTION_DETAILS",
                                    "REFUND",
                                    "AUTH_CAPTURE",
                                    "BUTTON_MANAGER"
                                ]
                            ],
                            //'first_party_details' => [
                            //    'features' => [
                            //        'PAYMENT',
                            //        'FUTURE_PAYMENT',
                            //        //'GRANT_PROXY_CLIENT',
                            //        'PARTNER_FEE',
                            //        'ACCESS_MERCHANT_INFORMATION',
                            //        'TRACKING_SHIPMENT_READWRITE',
                            //        'INVOICE_READ_WRITE',
                            //        'REFUND',
                            //    ],
                            //],
                        ],
                    ],
                ],
            ],
            'legal_consents' => [
                [
                    'type' => 'SHARE_DATA_CONSENT',
                    'granted' => true,
                ],
            ],
            'products' => [
                'EXPRESS_CHECKOUT',
            ],
        ];

        $response = $this->http()->post($this->url(self::PARTNER_REFERAL_ENDPOINT), $payload)->json();

        $link = collect($response['links'])->filter(fn ($link) => $link['rel'] == 'action_url')->first()['href'];

        return $link;
    }

    public function getMerchant($merchantPartnerId, $accountId)
    {
        $url = sprintf(self::SHOW_MERCHANT_ENDPOINT, $merchantPartnerId, $accountId);
        $account = $this->http()->get($this->url($url))->json();

        return [
            'id' => $account['merchant_id'],
            'products' => $account['products'],
            'is_able_to_accept_payments' => $account['payments_receivable'],
            'primary_email_confirmed' => $account['primary_email_confirmed'],
        ];
    }

    public function showOrder($orderId)
    {
        $url = sprintf(self::SHOW_ORDER_ENDPOINT, $orderId);

        return $this->http()->get($this->url($url))->json();
    }

    public function createOrder($amount, $currency)
    {
        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'value' => $amount,
                        'currency_code' => $currency,
                    ],
                    'payee' => [
                        'merchant_id' => $this->accountId,
                    ],
                    //'item' => [
                    //    'name' => $name,
                    //    'unit_amount' => $amount,
                    //],
                ],
            ],
        ];

        return $this->http()->post($this->url(self::CREATE_ORDER_ENDPOINT), $payload)->json();
    }
}
