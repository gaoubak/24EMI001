<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Agency;
use App\Entity\Sector;

#[ORM\Entity]
#[ORM\Table(name: "interventions")]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "bigint", unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $status;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Agency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Agency $agency;

    #[ORM\ManyToOne(targetEntity: Sector::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Sector $sector;

    #[ORM\Column(type: "bigint")]
    private int $scheduleId;

    public function getId(): ?int 
    { 
        return $this->id; 
    }
    public function getStatus(): string 
    { 
        return $this->status; 
    }
    public function setStatus(string $status): self 
    { 
        $this->status = $status; 
        return $this; 
    }
    public function getDescription(): string 
    { 
        return $this->description; 
    }
    public function setDescription(string $description): self 
    { 
        $this->description = $description; 
        return $this; 
    }
    public function getAgency(): Agency
    { 
        return $this->agency; 
    }
    public function setAgency(Agency $agency): self 
    { 
        $this->agency = $agency; 
        return $this; 
    }
    public function getSector(): Sector 
    { 
        return $this->sector; 
    }
    public function setSector(Sector $sector): self 
    { 
        $this->sector = $sector; 
        return $this; 
    }
    public function getScheduleId(): int 
    { 
        return $this->scheduleId; 
    }
    public function setScheduleId(int $scheduleId): self 
    { 
        $this->scheduleId = $scheduleId; 
        return $this; 
    }
}
