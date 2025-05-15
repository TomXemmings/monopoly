<?php

namespace App\Enums;

enum PaymentTypeEnum: string
{
    case NotSet     = 'NotSet';
    case Prepayment = 'Prepayment';
    case Postpay    = 'Postpay';
}
