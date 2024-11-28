<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Attribute\BlindIndexed;
use App\Attribute\Encrypted;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(
    fields: ['emailBlindIndex'],
    message: 'Cet email est déjà utilisé'
)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_user', 'get_forms', 'get_form'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[BlindIndexed(targetPropertyName: 'emailBlindIndex')]
    #[Groups(['get_user', 'get_forms', 'get_form'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_user'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['get_user'])]
    private ?string $emailBlindIndex = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Groups(['get_user'])]
    private ?string $passwordCreationToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['get_user'])]
    private ?\DateTimeInterface $passwordCreationDateExpiration = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_user', 'get_forms', 'get_form'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_user', 'get_forms', 'get_form'])]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['get_user', 'get_forms', 'get_form'])]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex('/^\+?[0-9]{1,4}?[-. ]?([0-9]{1,3})?[-. ]?([0-9]{1,4})[-. ]?([0-9]{1,4})$/', message: 'Invalid phone number format')]
    #[Groups(['get_user', 'get_forms', 'get_form'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?array $roles = null;

    #[ORM\ManyToOne(targetEntity: Agency::class, inversedBy: "users")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Agency $agency = null;

    #[ORM\OneToOne(targetEntity: UploadFile::class)]
    #[ORM\JoinColumn(name: "photo_identity", referencedColumnName: "id", nullable: true)]
    private ?UploadFile $photoIdentity = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmailBlindIndex(): ?string
    {
        return $this->emailBlindIndex;
    }

    public function setEmailBlindIndex(string $emailBlindIndex): static
    {
        $this->emailBlindIndex = $emailBlindIndex;

        return $this;
    }

    public function getPasswordCreationToken(): ?string
    {
        return $this->passwordCreationToken;
    }

    public function setPasswordCreationToken(?string $passwordCreationToken): static
    {
        $this->passwordCreationToken = $passwordCreationToken;

        return $this;
    }

    public function getPasswordCreationDateExpiration(): ?\DateTimeInterface
    {
        return $this->passwordCreationDateExpiration;
    }

    public function setPasswordCreationDateExpiration(?\DateTimeInterface $passwordCreationDateExpiration): static
    {
        $this->passwordCreationDateExpiration = $passwordCreationDateExpiration;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
   
    public function getUserIdentifier(): string
    {
        return (string) $this->emailBlindIndex;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;
        return $this;
    }

    public function getPhotoIdentity():?UploadFile
    {
        return $this->photoIdentity;
    }

    public function setPhotoIdentity(?UploadFile $photoIdentity): self
    {
        $this->photoIdentity = $photoIdentity;
        return $this;
    }


    public function eraseCredentials(): void
    {

    }

}