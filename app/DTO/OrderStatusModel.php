<?php

namespace App\DTO;

use App\Enums\OrderStatusEnum;

final readonly class OrderStatusModel
{
    public function __construct(
        public OrderStatusEnum $status,
        public ?float          $refueledVolume = null,
        public ?float          $cost           = null,
        public ?string         $cancelReason   = null,
        public string          $updatedAt,
    ) {}
}
