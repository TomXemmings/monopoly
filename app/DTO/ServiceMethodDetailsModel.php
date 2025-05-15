<?php

namespace App\DTO;

use App\Enums\ServiceMethodEnum;

final readonly class ServiceMethodDetailsModel
{
    /**
     * @param FuelingLimitModel[]|null $fuelingLimits
     */
    public function __construct(
        public ServiceMethodEnum       $serviceMethod,
        public ?RefuelingScenarioModel $refuelingScenario = null,
        public ?array                  $fuelingLimits     = null,
    ) {}
}
