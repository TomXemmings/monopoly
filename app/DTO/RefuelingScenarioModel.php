<?php

namespace App\DTO;

final readonly class RefuelingScenarioModel
{
    public function __construct(
        public string $nozzleState,
    ) {}
}
