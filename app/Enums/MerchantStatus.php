<?php

namespace App\Enums;

//enum MerchantStatus: string
//{
//    case NONE = 'none';
//    case PENDING = 'pending';
//    case CONNECTED = 'connected';
//    case DISCONNECTED = 'disconnected';
//}

class MerchantStatus extends Enum
{
    const NONE = 'none';

    const PENDING = 'pending';

    const CONNECTED = 'connected';

    const DISCONNECTED = 'disconnected';
}
