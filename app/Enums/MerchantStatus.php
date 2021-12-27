<?php

namespace App\Enums;

class MerchantStatus extends Enum
{
    const NONE = 'none';

    const PENDING = 'pending';

    const CONNECTED = 'connected';

    const DISCONNECTED = 'disconnected';
}
