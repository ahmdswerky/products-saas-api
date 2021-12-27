<?php

namespace App\Enums;

class PaymentStatus extends Enum
{
    const PENDING = 'pending';

    const SUCCEEDED = 'succeeded';

    const CANCELED = 'canceled';

    const FAILED = 'failed';

    const REFUNDED = 'refunded';
}
