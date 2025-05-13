<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\ManyToOne(inversedBy: 'receivedReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $about = null;

    #[ORM\ManyToOne(inversedBy: 'reviewsGiven')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reviewGiver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewGiver(): ?User
    {
        return $this->reviewGiver;
    }

    public function setReviewGiver(?User $reviewGiver): static
    {
        $this->reviewGiver = $reviewGiver;

        return $this;
    }

    public function getReviewReceiver(): ?User
    {
        return $this->reviewReceiver;
    }

    public function setReviewReceiver(?User $reviewReceiver): static
    {
        $this->reviewReceiver = $reviewReceiver;
        return $this;
    }
}
