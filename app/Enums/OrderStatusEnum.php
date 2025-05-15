<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case Unknown   = 'Unknown';
    case Created   = 'Created';
    case Accepted  = 'Accepted';
    case Fueling   = 'Fueling';
    case Completed = 'Completed';
    case Canceled  = 'Canceled';
    case Failed    = 'Failed';
}
