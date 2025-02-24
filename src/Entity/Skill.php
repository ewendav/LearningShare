<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $searchCounter = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'skillTaught')]
    private Collection $sessionsTaught;

    #[ORM\ManyToOne(inversedBy: 'skills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'skillRequested')]
    private Collection $exchangesRequested;

    public function __construct()
    {
        $this->sessionsTaught = new ArrayCollection();
        $this->exchangesRequested = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSearchCounter(): ?int
    {
        return $this->searchCounter;
    }

    public function setSearchCounter(int $searchCounter): static
    {
        $this->searchCounter = $searchCounter;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessionsTaught(): Collection
    {
        return $this->sessionsTaught;
    }

    public function addSessionsTaught(Session $sessionsTaught): static
    {
        if (!$this->sessionsTaught->contains($sessionsTaught)) {
            $this->sessionsTaught->add($sessionsTaught);
            $sessionsTaught->setSkillTaught($this);
        }

        return $this;
    }

    public function removeSessionsTaught(Session $sessionsTaught): static
    {
        if ($this->sessionsTaught->removeElement($sessionsTaught)) {
            // set the owning side to null (unless already changed)
            if ($sessionsTaught->getSkillTaught() === $this) {
                $sessionsTaught->setSkillTaught(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getExchangesRequested(): Collection
    {
        return $this->exchangesRequested;
    }

    public function addExchangesRequested(Exchange $exchangesRequested): static
    {
        if (!$this->exchangesRequested->contains($exchangesRequested)) {
            $this->exchangesRequested->add($exchangesRequested);
            $exchangesRequested->setSkillRequested($this);
        }

        return $this;
    }

    public function removeExchangesRequested(Exchange $exchangesRequested): static
    {
        if ($this->exchangesRequested->removeElement($exchangesRequested)) {
            // set the owning side to null (unless already changed)
            if ($exchangesRequested->getSkillRequested() === $this) {
                $exchangesRequested->setSkillRequested(null);
            }
        }

        return $this;
    }
}
