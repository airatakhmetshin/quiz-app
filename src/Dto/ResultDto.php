<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\QuestionResultStatusEnum as StatusEnum;

final readonly class ResultDto
{
    public function __construct(
        public int        $id,
        public StatusEnum $status,
        public int        $questionID,
        public string     $questionText,
    ) {
    }
}
