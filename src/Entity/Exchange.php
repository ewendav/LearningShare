<?php

namespace App\Entity;

use App\Repository\ExchangeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRepository::class)]
class Exchange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'exchangesRequested')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Skill $skillRequested = null;

    #[ORM\ManyToOne(inversedBy: 'exchangesCreated')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $requester = null;

    #[ORM\ManyToOne(inversedBy: 'exchangesAttended')]
    private ?User $attendee = null;

    #[ORM\OneToOne(mappedBy: 'exchange', cascade: ['persist', 'remove'])]
    private ?Session $session = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkillRequested(): ?Skill
    {
        return $this->skillRequested;
    }

    public function setSkillRequested(?Skill $skillRequested): static
    {
        $this->skillRequested = $skillRequested;

        return $this;
    }

    public function getRequester(): ?User
    {
        return $this->requester;
    }

    public function setRequester(?User $requester): static
    {
        $this->requester = $requester;

        return $this;
    }

    public function getAttendee(): ?User
    {
        return $this->attendee;
    }

    public function setAttendee(?User $attendee): static
    {
        $this->attendee = $attendee;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        // unset the owning side of the relation if necessary
        if ($session === null && $this->session !== null) {
            $this->session->setExchange(null);
        }

        // set the owning side of the relation if necessary
        if ($session !== null && $session->getExchange() !== $this) {
            $session->setExchange($this);
        }

        $this->session = $session;

        return $this;
    }
}
