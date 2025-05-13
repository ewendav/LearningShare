<?php

namespace App\Twig\Components;

use App\Repository\SessionRepository;
use DateTime;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Bundle\SecurityBundle\Security;
// must be imported
use App\Entity\Session;
use App\Entity\User;

#[AsLiveComponent]
final class DisplaySessions
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public bool $lessonChecked = false;

    #[LiveProp]
    /** @var Session[] */
    public array $lessons = [];

    #[LiveProp]
    /** @var Session[] */
    public array $exchanges = [];

    #[LiveProp(writable: true)]
    public string $q = '';

    #[LiveProp(writable: true)]
    public string $category = '';

    #[LiveProp(writable: true)]
    public string $skillFilter = '';

    #[LiveProp(writable: true, format: 'Y-m-d')]
    public ?\DateTime $dateStart = null;

    #[LiveProp(writable: true, format: 'Y-m-d')]
    public ?\DateTimeInterface $dateEnd = null;

    #[LiveProp(writable: true)]
    public string $timeStart = '';

    #[LiveProp(writable: true)]
    public string $timeEnd = '';

    private SessionRepository $sessionRepository;
    private Security $security;

    public function __construct(
        SessionRepository $sessionRepository,
        Security $security
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->security = $security;
    }

    public function mount(): void
    {
        $user = $this->security->getUser();
        $this->fetchSessions($user);
        $this->q = $_GET['q'] ?? '';
    }

    #[LiveAction]
    public function toggleLessonChecked(): void
    {
        $user = $this->security->getUser();
        $this->fetchSessions($user);
    }

    #[LiveAction]
    public function search(): void
    {
        $user = $this->security->getUser();
        $this->fetchSessions($user);
    }


    private function fetchCategories(): array
    {
        $this->lessons = $this->sessionRepository->searchLessons(
            $user,
            $this->q,
            $this->category,
            $this->dateStart,
            $this->dateEnd,
            $this->timeStart,
            $this->timeEnd
        );
        $this->exchanges = [];
    }

    private function fetchSessions(?User $user): void
    {
        if ($this->lessonChecked) {
            $this->lessons = $this->sessionRepository->searchLessons(
                $user,
                $this->q,
                $this->category,
                $this->dateStart,
                $this->dateEnd,
                $this->timeStart,
                $this->timeEnd
            );
            $this->exchanges = [];
        } else {
            $this->exchanges = $this->sessionRepository->searchExchanges(
                $user,
                $this->q,
                $this->category,
                $this->skillFilter,
                $this->dateStart,
                $this->dateEnd,
                $this->timeStart,
                $this->timeEnd
            );
            $this->lessons = [];
        }
    }
}
