<?php

namespace App\Entity;

use App\Enum\QuestionResultStatusEnum as StatusEnum;
use App\Repository\QuestionResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: QuestionResultRepository::class)]
#[ORM\Table(name: '`question_result`')]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['session_id', 'question_id'])]
class QuestionResultEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'session_id', length: 255)]
    /** uniq index session_id + question_id */
    private ?string $sessionId = null;

    #[ORM\Column(name: 'question_id')]
    private ?int $questionId = null;

    #[ORM\Column(name: 'answer_ids', type: 'json')]
    private array $answerIds = [];

    #[ORM\Column(name: 'status', type: 'string', length: 10)]
    private ?string $status = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getQuestionId(): ?int
    {
        return $this->questionId;
    }

    public function setQuestionId(int $questionId): static
    {
        $this->questionId = $questionId;

        return $this;
    }

    public function getAnswerIds(): array
    {
        return $this->answerIds;
    }

    public function setAnswerIds(array $answerIds): static
    {
        $this->answerIds = $answerIds;

        return $this;
    }

    public function getStatus(): StatusEnum
    {
        return StatusEnum::from($this->status);
    }

    public function setStatus(StatusEnum $status): static
    {
        $this->status = $status->value;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
