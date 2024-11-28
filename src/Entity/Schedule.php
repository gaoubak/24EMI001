<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "schedules")]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "bigint", unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $endTime;

    #[ORM\Column(type: "bigint")]
    private int $status;

    #[ORM\Column(type: "bigint")]
    private int $intervenantId;

    #[ORM\Column(type: "bigint")]
    private int $interventionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getIntervenantId(): int
    {
        return $this->intervenantId;
    }

    public function setIntervenantId(int $intervenantId): self
    {
        $this->intervenantId = $intervenantId;
        return $this;
    }

    public function getInterventionId(): int
    {
        return $this->interventionId;
    }

    public function setInterventionId(int $interventionId): self
    {
        $this->interventionId = $interventionId;
        return $this;
    }
}
