<?php

namespace Integration\Service;

use App\Service\QuestionService;
use App\SessionStorage\QuizSession;
use App\Tests\Support\IntegrationTester;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class QuestionServiceTest extends \Codeception\Test\Unit
{

    protected IntegrationTester $tester;
    protected QuestionService $questionService;
    protected QuizSession $quizSession;

    protected function _before(): void
    {
        $this->questionService = $this->tester->grabService(QuestionService::class);
        $this->quizSession = $this->makeQuizSession();
    }

    protected function makeQuizSession(): QuizSession
    {
        $session = $this->makeEmpty(SessionInterface::class);
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getSession')
            ->willReturn($session);

        return new QuizSession($requestStack);
    }

    public function testEmptyProgress(): void
    {
        $sessionProgressDto = $this->questionService->hasActiveQuizSession($this->quizSession);
        $this->assertEquals(0, $sessionProgressDto->progress);
    }
}
