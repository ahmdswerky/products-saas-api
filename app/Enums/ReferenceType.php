<?php

namespace App\Enums;

class ReferenceType extends Enum
{
    const CUSTOMER_ID = 'customer_id';

    const CUSTOMER_EMAIL = 'customer_email';

    const MERCHANT_ID = 'merchant_id';

    const ORDER_ID = 'order_id';

    const CHARGE = 'charge';

    const PAYMENT_METHOD = 'payment_method';

    //const PAYMENT_INTENT = 'payment_intent';

    //public static $methods = [
    //    'credit_card' => [
    //        self::ORDER_ID => self::PAYMENT_INTENT,
    //    ],
    //    'paypal' => [
    //        self::ORDER_ID => self::ORDER_ID,
    //    ],
    //];
}
