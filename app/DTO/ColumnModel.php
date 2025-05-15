<?php

namespace App\DTO;

use App\Enums\FuelTypeEnum;

final readonly class ColumnModel
{
    /**
     * @param FuelTypeEnum[]|null           $fuelTypes
     * @param array<int, FuelTypeEnum>|null $nozzles
     */
    public function __construct(
        public int    $columnId,
        public ?array $fuelTypes = null,
        public ?array $nozzles   = null,
    ) {}
}
