<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\UploadFile;
use App\Entity\User;

#[ORM\Entity]
#[ORM\Table(name: "user_uploaded_file")]
class UserUploadedFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "bigint", unsigned: true)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UploadFile::class)]
    #[ORM\JoinColumn(nullable: false)]
    private UploadFile $uploadFile;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadFile(): UploadFile
    {
        return $this->uploadFile;
    }

    public function setUploadFile(UploadFile $uploadFile): self
    {
        $this->uploadFile = $uploadFile;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
