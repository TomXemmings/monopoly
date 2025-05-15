<?php

namespace App\Enums;

enum FuelTypeEnum: string
{
    case A80           = 'a80';
    case A92           = 'a92';
    case A92Premium    = 'a92_premium';
    case A95           = 'a95';
    case A95Premium    = 'a95_premium';
    case A98           = 'a98';
    case A98Premium    = 'a98_premium';
    case A100          = 'a100';
    case A100Premium   = 'a100_premiuim';
    case Diesel        = 'diesel';
    case DieselPremium = 'diesel_premium';
    case DieselWinter  = 'diesel_winter';
    case Propane       = 'propane';
    case Metane        = 'metane';
}
