<?php

namespace App\DTO;

use App\Enums\FuelTypeEnum;
use App\Enums\ServiceMethodEnum;

final readonly class FuelPriceModel
{
    public function __construct(
        public string            $stationId,
        public FuelTypeEnum      $fuelType,
        public float             $fuelPrice,
        public ServiceMethodEnum $serviceMethod,
    ) {}
}
