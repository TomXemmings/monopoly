<?php

namespace App\Enums;

enum ServiceMethodEnum: string
{
    case Online    = 'Online';
    case MoBarCode = 'MoBarCode';
    case BarCode   = 'BarCode';
}
