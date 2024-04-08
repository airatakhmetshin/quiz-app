<?php

declare(strict_types=1);

namespace App\SessionStorage;

use Symfony\Component\HttpFoundation\RequestStack;

final class QuizSession
{
    private const QUIZ_SESSION_NAME = 'quiz_session';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getID(): string
    {
        $id = $this->requestStack->getSession()->get(self::QUIZ_SESSION_NAME);

        if (null === $id) {
            $id = self::generateID();
            $this->requestStack->getSession()->set(self::QUIZ_SESSION_NAME, $id);
        }

        return $id;
    }

    public function resetID(): void
    {
        $id = self::generateID();
        $this->requestStack->getSession()->set(self::QUIZ_SESSION_NAME, $id);
    }

    private static function generateID(): string
    {
        return uniqid('', true);
    }
}
