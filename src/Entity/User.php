<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Length(min: 6)]
    private ?string $plainPassword = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $biography = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarPath = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $balance = null;

    /**
     * @var Collection<int, Lesson>
     */
    #[ORM\ManyToMany(targetEntity: Lesson::class, inversedBy: 'attendees')]
    private Collection $lessonsAttended;

    /**
     * @var Collection<int, Lesson>
     */
    #[ORM\OneToMany(targetEntity: Lesson::class, mappedBy: 'host')]
    private Collection $lessonsHosted;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'requester')]
    private Collection $exchangesCreated;

    /**
     * @var Collection<int, Exchange>
     */
    #[ORM\OneToMany(targetEntity: Exchange::class, mappedBy: 'attendee')]
    private Collection $exchangesAttended;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'reviewGiver')]
    private Collection $reviewsGiven;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'reviewReceiver')]
    private Collection $receivedReviews;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'ReportGiver')]
    private Collection $reportsGiven;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'reportReceiver')]
    private Collection $reportsReceived;

    public function __construct()
    {
        $this->lessonsAttended = new ArrayCollection();
        $this->lessonsHosted = new ArrayCollection();
        $this->exchangesCreated = new ArrayCollection();
        $this->exchangesAttended = new ArrayCollection();
        $this->reviewsGiven = new ArrayCollection();
        $this->receivedReviews = new ArrayCollection();
        $this->reportsGiven = new ArrayCollection();
        $this->reportsReceived = new ArrayCollection();
    }

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getFullName(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;

        return $this;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(?string $avatarPath): static
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessonsAttended(): Collection
    {
        return $this->lessonsAttended;
    }

    public function addLessonsAttended(Lesson $lessonsAttended): static
    {
        if (!$this->lessonsAttended->contains($lessonsAttended)) {
            $this->lessonsAttended->add($lessonsAttended);
            $lessonsAttended->addAttendee($this);
        }

        return $this;
    }

    public function removeLessonsAttended(Lesson $lessonsAttended): static
    {
        if ($this->lessonsAttended->removeElement($lessonsAttended)) {
            // Assurez-vous que la relation inverse est également mise à jour
            $lessonsAttended->removeAttendee($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessonsHosted(): Collection
    {
        return $this->lessonsHosted;
    }

    public function addLessonsHosted(Lesson $lessonsHosted): static
    {
        if (!$this->lessonsHosted->contains($lessonsHosted)) {
            $this->lessonsHosted->add($lessonsHosted);
            $lessonsHosted->setHost($this);
        }

        return $this;
    }

    public function removeLessonsHosted(Lesson $lessonsHosted): static
    {
        if ($this->lessonsHosted->removeElement($lessonsHosted)) {
            // set the owning side to null (unless already changed)
            if ($lessonsHosted->getHost() === $this) {
                $lessonsHosted->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getExchangesCreated(): Collection
    {
        return $this->exchangesCreated;
    }

    public function addExchangesCreated(Exchange $exchangesCreated): static
    {
        if (!$this->exchangesCreated->contains($exchangesCreated)) {
            $this->exchangesCreated->add($exchangesCreated);
            $exchangesCreated->setRequester($this);
        }

        return $this;
    }

    public function removeExchangesCreated(Exchange $exchangesCreated): static
    {
        if ($this->exchangesCreated->removeElement($exchangesCreated)) {
            // set the owning side to null (unless already changed)
            if ($exchangesCreated->getRequester() === $this) {
                $exchangesCreated->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getExchangesAttended(): Collection
    {
        return $this->exchangesAttended;
    }

    public function addExchangesAttended(Exchange $exchangesAttended): static
    {
        if (!$this->exchangesAttended->contains($exchangesAttended)) {
            $this->exchangesAttended->add($exchangesAttended);
            $exchangesAttended->setAttendee($this);
        }

        return $this;
    }

    public function removeExchangesAttended(Exchange $exchangesAttended): static
    {
        if ($this->exchangesAttended->removeElement($exchangesAttended)) {
            // set the owning side to null (unless already changed)
            if ($exchangesAttended->getAttendee() === $this) {
                $exchangesAttended->setAttendee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviewsGiven(): Collection
    {
        return $this->reviewsGiven;
    }

    public function addReviewsGiven(Review $reviewsGiven): static
    {
        if (!$this->reviewsGiven->contains($reviewsGiven)) {
            $this->reviewsGiven->add($reviewsGiven);
            $reviewsGiven->setReviewGiver($this);
        }

        return $this;
    }

    public function removeReviewsGiven(Review $reviewsGiven): static
    {
        if ($this->reviewsGiven->removeElement($reviewsGiven)) {
            // set the owning side to null (unless already changed)
            if ($reviewsGiven->getReviewGiver() === $this) {
                $reviewsGiven->setReviewGiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReceivedReviews(): Collection
    {
        return $this->receivedReviews;
    }

    public function addReceivedReview(Review $review): static
    {
        if (!$this->receivedReviews->contains($review)) {
            $this->receivedReviews->add($review);
            $review->setReviewReceiver($this);
        }

        return $this;
    }

    public function removeReceivedReview(Review $review): static
    {
        if ($this->receivedReviews->removeElement($review)) {
            if ($review->getReviewReceiver() === $this) {
                $review->setReviewReceiver(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Report>
     */
    public function getReportsGiven(): Collection
    {
        return $this->reportsGiven;
    }

    public function addReportsGiven(Report $reportsGiven): static
    {
        if (!$this->reportsGiven->contains($reportsGiven)) {
            $this->reportsGiven->add($reportsGiven);
            $reportsGiven->setReportGiver($this);
        }

        return $this;
    }

    public function removeReportsGiven(Report $reportsGiven): static
    {
        if ($this->reportsGiven->removeElement($reportsGiven)) {
            // set the owning side to null (unless already changed)
            if ($reportsGiven->getReportGiver() === $this) {
                $reportsGiven->setReportGiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReportsReceived(): Collection
    {
        return $this->reportsReceived;
    }

    public function addReportsReceived(Report $reportsReceived): static
    {
        if (!$this->reportsReceived->contains($reportsReceived)) {
            $this->reportsReceived->add($reportsReceived);
            $reportsReceived->setReportReceiver($this);
        }

        return $this;
    }

    public function removeReportsReceived(Report $reportsReceived): static
    {
        if ($this->reportsReceived->removeElement($reportsReceived)) {
            // set the owning side to null (unless already changed)
            if ($reportsReceived->getReportReceiver() === $this) {
                $reportsReceived->setReportReceiver(null);
            }
        }

        return $this;
    }
}
