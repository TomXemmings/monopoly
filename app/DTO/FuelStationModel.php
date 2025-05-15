<?php

namespace App\DTO;

final readonly class FuelStationModel
{
    public function __construct(
        public string $id,
        public string $name,
        public string $brand,
        public bool   $isAvailable,
        public string $address,
        public float  $longitude,
        public float  $lattitude,
        public array  $serviceMethods,
    ) {}
}
