<?php

namespace App\Twig\Components;

use App\Repository\SessionRepository;
use App\Repository\CategoryRepository;
use DateTime;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Bundle\SecurityBundle\Security;
// must be imported
use App\Entity\Session;
use App\Entity\Category;
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
    /** @var Category[] */
    public array $categories = [];

    #[LiveProp(writable: true)]
    public string $q = '';

    #[LiveProp(writable: true)]
    public string $categoryGiven = '';

    #[LiveProp(writable: true)]
    public string $categoryRequested = '';

    #[LiveProp(writable: true)]
    public string $skillGiven = '';

    #[LiveProp(writable: true)]
    public string $skillRequested = '';

    #[LiveProp(writable: true, format: 'Y-m-d')]
    public ?\DateTime $dateStart = null;

    #[LiveProp(writable: true, format: 'Y-m-d')]
    public ?\DateTimeInterface $dateEnd = null;

    #[LiveProp(writable: true)]
    public string $timeStart = '';

    #[LiveProp(writable: true)]
    public string $timeEnd = '';

    private SessionRepository $sessionRepository;
    private CategoryRepository $categoryRepository;
    private Security $security;

    public function __construct(
        SessionRepository $sessionRepository,
        CategoryRepository $categoryRepository,
        Security $security
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->security = $security;
    }

    public function mount(): void
    {
        $user = $this->security->getUser();
        if (empty($this->categories)) {
            $this->fetchCategories();
            dump($this->categories);
        }
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

    private function fetchCategories(): void
    {
        $this->categories = $this->categoryRepository->findAll();
    }

    private function fetchSessions(?User $user): void
    {
        $categoryGiven = array_filter(
            $this->categories,
            fn ($cat) =>  $cat->getId() === (int)($this->categoryGiven)
        );
        $categoryGiven = $categoryGiven ? array_values($categoryGiven)[0] : null;

        if ($this->lessonChecked) {
            $this->lessons = $this->sessionRepository->searchLessons(
                $user,
                $this->q,
                $categoryGiven,
                $this->skillGiven,
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
                $categoryGiven,
                $categoryRequested,
                $this->skillGiven,
                $this->skillRequested,
                $this->dateStart,
                $this->dateEnd,
                $this->timeStart,
                $this->timeEnd
            );
            $this->lessons = [];
        }
    }
}
