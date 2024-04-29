<?php

namespace Unit\Service\QuestionService;

use App\Entity\AnswerEntity;
use App\Enum\QuestionResultStatusEnum;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuestionResultRepository;
use App\Service\QuestionService;
use App\Tests\Support\UnitTester;
use Doctrine\ORM\EntityManagerInterface;

class SuccessOrFailedTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected EntityManagerInterface $em;
    protected QuestionRepository $questionRepository;
    protected QuestionResultRepository $questionResultRepository;

    protected function _before(): void
    {
        $this->em = $this->makeEmpty(EntityManagerInterface::class);
        $this->questionRepository = $this->make(QuestionRepository::class);
        $this->questionResultRepository = $this->make(QuestionResultRepository::class);
    }

    protected function successOrFailed(array $answers, array $selectedAnswers): QuestionResultStatusEnum
    {
        $answerRepository = $this->createMock(AnswerRepository::class);
        $answerRepository->method('findBy')
            ->willReturn($answers);

        $service = new QuestionService($this->em, $answerRepository, $this->questionRepository, $this->questionResultRepository);

        $class = new \ReflectionClass($service);
        $method = $class->getMethod('successOrFailed');

        return $method->invokeArgs($service, [1, $selectedAnswers]);
    }

    public function testFullMatch(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, [1, 2]);
        $this->assertEquals(QuestionResultStatusEnum::SUCCESS, $result);
    }

    public function testMoreCorrectThanSelected(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, [1]);
        $this->assertEquals(QuestionResultStatusEnum::SUCCESS, $result);
    }

    public function testMoreSelectedThanCorrect(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, [1, 2]);
        $this->assertEquals(QuestionResultStatusEnum::FAILED, $result);
    }

    public function testMoreSelectedThanCorrectExtended(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(true),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, [1, 2, 3]);
        $this->assertEquals(QuestionResultStatusEnum::FAILED, $result);
    }

    public function testZeroAndZero(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, []);
        $this->assertEquals(QuestionResultStatusEnum::SUCCESS, $result);
    }

    public function testZeroAndSelected(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, [1]);
        $this->assertEquals(QuestionResultStatusEnum::FAILED, $result);
    }

    public function testZeroAndSelectedExtended(): void
    {
        $answers = [
            $this->make(AnswerEntity::class, ['id' => 1])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 2])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 3])->setCorrect(false),
            $this->make(AnswerEntity::class, ['id' => 4])->setCorrect(false),
        ];

        $result = $this->successOrFailed($answers, [1, 2]);
        $this->assertEquals(QuestionResultStatusEnum::FAILED, $result);
    }
}
