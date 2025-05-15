<?php

namespace App\DTO;

use App\Enums\FuelTypeEnum;

final readonly class FuelingLimitModel
{
    public function __construct(
        public FuelTypeEnum $fuelType,
        public float        $minVolume,
        public float        $maxVolume,
    ) {}
}
