<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    private ?int $maxAttendees = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    private ?Location $location = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'lessonsAttended')]
    private Collection $attendees;

    #[ORM\ManyToOne(inversedBy: 'lessonsHosted')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $host = null;

    #[ORM\OneToOne(mappedBy: 'lesson', cascade: ['persist', 'remove'])]
    private ?Session $session = null;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxAttendees(): ?int
    {
        return $this->maxAttendees;
    }

    public function setMaxAttendees(int $maxAttendees): static
    {
        $this->maxAttendees = $maxAttendees;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(User $attendee): static
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
            // Éviter l'appel récursif si l'utilisateur a déjà cette leçon dans sa collection
            if (!$attendee->getLessonsAttended()->contains($this)) {
                $attendee->getLessonsAttended()->add($this);
            }
        }

        return $this;
    }

    public function removeAttendee(User $attendee): static
    {
        if ($this->attendees->removeElement($attendee)) {
            $attendee->removeLessonsAttended($this);
        }

        return $this;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        $this->host = $host;

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
            $this->session->setLesson(null);
        }

        // set the owning side of the relation if necessary
        if ($session !== null && $session->getLesson() !== $this) {
            $session->setLesson($this);
        }

        $this->session = $session;

        return $this;
    }
}
