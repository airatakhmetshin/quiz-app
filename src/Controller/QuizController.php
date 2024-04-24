<?php

namespace App\Controller;

use App\Dto\SubmitQuestionDto;
use App\Service\QuestionService;
use App\SessionStorage\QuizSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

class QuizController extends AbstractController
{
    public function __construct(
        private readonly QuestionService $questionService,
    ) {
    }

    #[Route('/', name: 'app_quiz_start', methods: ['GET'])]
    public function index(QuizSession $quizSession): Response
    {
        $progress = $this->questionService->hasActiveQuizSession($quizSession);

        return $this->render('pages/start.html.twig', ['progress' => $progress]);
    }

    #[Route('/question', name: 'app_quiz_next', methods: ['GET'])]
    public function next(QuizSession $quizSession): Response
    {
        ['question' => $question, 'answers' => $answers] = $this->questionService->getQuestion(
            quizSession: $quizSession,
            answerShuffle: true,
        );

        if (null === $question) {
            return $this->redirectToRoute('app_quiz_result');
        }

        return $this->render('pages/question.html.twig', [
            'question' => $question,
            'answers'  => $answers,
        ]);
    }

    #[Route('/question', name: 'app_quiz_submit', methods: ['POST'])]
    public function submit(
        Request     $request,
        QuizSession $quizSession,
    ): Response {
        $questionID = (int) $request->get('question_id');
        $answerIDs  = array_map(static fn(string $id): int => (int) $id, $request->get('answer_ids', []));

        $this->questionService->submit(new SubmitQuestionDto(
            quizSession: $quizSession,
            questionID: $questionID,
            answerIDs: $answerIDs,
        ));

        return $this->questionService->hasQuestions($quizSession)
            ? $this->redirectToRoute('app_quiz_next')
            : $this->redirectToRoute('app_quiz_result');
    }

    #[Route('/result', name: 'app_quiz_result', methods: ['GET'])]
    public function result(QuizSession $quizSession): Response
    {
        $hasQuestions = $this->questionService->hasQuestions($quizSession);

        if ($hasQuestions) {
            return $this->redirectToRoute('app_quiz_start');
        }

        $result = $this->questionService->getResult($quizSession);

        return $this->render('pages/result.html.twig', ['result' => $result]);
    }

    #[Route('/new', name: 'app_quiz_new', methods: ['GET'])]
    public function new(QuizSession $quizSession): Response
    {
        $quizSession->resetID();

        return $this->redirectToRoute('app_quiz_next');
    }
}
