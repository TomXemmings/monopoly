<?php

namespace App\DTO;

use App\Enums\PaymentTypeEnum;

final readonly class FuelStationDetailsModel
{
    public function __construct(
        public string          $id,
        public string          $name,
        public string          $brand,
        public bool            $isAvailable,
        public string          $address,
        public float           $longitude,
        public float           $lattitude,
        public PaymentTypeEnum $paymentType,
        public ?array          $columns,
        public array           $serviceMethods,
    ) {}
}
