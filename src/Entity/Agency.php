<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "agence")]
class Agency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "bigint", unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $contactClient;

    #[ORM\Column(type: "boolean")]
    private bool $isOutsourced;    

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $adresse = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getContactClient(): string
    {
        return $this->contactClient;
    }

    public function setContactClient(string $contactClient): self
    {
        $this->contactClient = $contactClient;
        return $this;
    }

    public function getIsOutsourced(): bool
    {
        return $this->isOutsourced;
    }

    public function setIsOutsourced(bool $isOutsourced): self
    {
        $this->isOutsourced = $isOutsourced;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }
}
