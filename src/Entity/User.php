<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudoMinecraft = null;

    #[ORM\Column(length: 255)]
    private ?string $uuidMinecraft = null;

    #[ORM\Column]
    private ?int $credits = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudoMinecraft(): ?string
    {
        return $this->pseudoMinecraft;
    }

    public function setPseudoMinecraft(string $pseudoMinecraft): static
    {
        $this->pseudoMinecraft = $pseudoMinecraft;

        return $this;
    }

    public function getUuidMinecraft(): ?string
    {
        return $this->uuidMinecraft;
    }

    public function setUuidMinecraft(string $uuidMinecraft): static
    {
        $this->uuidMinecraft = $uuidMinecraft;

        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): static
    {
        $this->credits = $credits;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeImmutable $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }
}
