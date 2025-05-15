<?php

namespace App\DTO;

use App\Enums\ColumnStateEnum;
use App\Enums\FuelTypeEnum;

final readonly class FuelStationColumnStatusModel
{
    public function __construct(
        public ColumnStateEnum $state,
        public ?float          $refueledVolume = null,
        public ?FuelTypeEnum   $fuelType       = null,
    ) {}
}
