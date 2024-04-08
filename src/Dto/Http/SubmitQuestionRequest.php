<?php

declare(strict_types=1);

namespace App\Dto\Http;

final readonly class SubmitQuestionRequest
{
    public function __construct(
        public string $question_id,
        public array  $answer_ids,
    ) {
    }
}
