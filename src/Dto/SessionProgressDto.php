<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class SessionProgressDto
{
    public function __construct(
        public int $total,
        public int $progress,
    ) {
    }
}
