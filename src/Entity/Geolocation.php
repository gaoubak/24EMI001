<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "geolocations")]
class Geolocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "bigint", unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: "point")]
    private string $location;

    #[ORM\Column(type: "integer")]
    private int $intervenantId;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $timestamp;

    public function __construct ()
    {
        $this->timestamp = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
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

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
