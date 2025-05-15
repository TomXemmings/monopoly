<?php

namespace App\DTO;

final readonly class BarCodeModel
{
    public function __construct(
        public string $expiredAt,
        public string $content,
        public string $type,
    ) {}
}

