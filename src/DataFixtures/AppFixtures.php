<?php

namespace App\DataFixtures;

use App\Entity\AnswerEntity;
use App\Entity\QuestionEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $questions = [
            '1 + 1'   => ['3' => false, '2' => true, '0' => false],
            '2 + 2'   => ['4' => true, '3 + 1' => true, '10' => false],
            '3 + 3'   => ['1 + 5' => true, '1' => false, '6' => true, '2 + 4' => true],
            '4 + 4'   => ['8' => true, '4' => false, '0' => false, '0 + 8' => true],
            '5 + 5'   => ['6' => false, '18' => false, '10' => true, '9' => false, '0' => false],
            '6 + 6'   => ['3' => false, '9' => false, '0' => false, '12' => true, '5 + 7' => true],
            '7 + 7'   => ['5' => false, '14' => true],
            '8 + 8'   => ['16' => true, '12' => false, '9' => false, '5' => false],
            '9 + 9'   => ['18' => true, '9' => false, '17 + 1' => true, '2 + 16' => true],
            '10 + 10' => ['0' => false, '2' => false, '8' => false, '20' => true],
            // ...
        ];

        foreach ($questions as $questionText => $answers) {
            $newQuestion = new QuestionEntity();
            $newQuestion->setText($questionText);

            foreach ($answers as $answerText => $answerCorrect) {
                $newAnswer = new AnswerEntity();
                $newAnswer->setQuestion($newQuestion);
                $newAnswer->setText($answerText);
                $newAnswer->setCorrect($answerCorrect);
                $manager->persist($newAnswer);
            }

            $manager->persist($newQuestion);
        }

        $manager->flush();
    }
}
