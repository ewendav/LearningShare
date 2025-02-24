<?php

namespace App\Entity;

use App\Repository\BlacklistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlacklistRepository::class)]
class Blacklist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?array $badWords = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBadWords(): ?array
    {
        return $this->badWords;
    }

    public function setBadWords(?array $badWords): static
    {
        $this->badWords = $badWords;

        return $this;
    }
}
