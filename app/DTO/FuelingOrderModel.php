<?php

namespace App\DTO;

use App\Enums\FuelTypeEnum;
use App\Enums\ServiceMethodEnum;

final readonly class FuelingOrderModel
{
    public function __construct(
        public string            $stationId,
        public ?int              $columnId,
        public ?int              $nozzleId,
        public FuelTypeEnum      $fuelType,
        public float             $refuelVolume,
        public ServiceMethodEnum $serviceMethod,
        public string            $userId,
    ) {}
}
