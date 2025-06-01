<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\SessionRepository;
use App\Repository\CategoryRepository;

// must be imported

#[AsLiveComponent]
final class Sidebar
{
    use DefaultActionTrait;

    private SessionRepository $sessionRepository;
    private Security $security;
    private CategoryRepository $categoryRepository;

    /**
     * An indexed array of categories for template usage.
     * @var array<int, array{name: string}>
     */
    public array $categoriesIndexed = [];

    public function __construct(
        SessionRepository $sessionRepository,
        Security $security,
        CategoryRepository $categoryRepository
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->security = $security;
        $this->categoryRepository = $categoryRepository;
    }

    /**
        * @LiveProp(writable: false)
        */
    public array $userSessions = [
        'cours'   => [],
        'partage' => [],
    ];

    public function mount(): void
    {
        $user = $this->security->getUser();
        if (null !== $user) {
            $this->userSessions = $this->sessionRepository
                ->getAttendableSessionsByUserEntities($user);
        }

        // Generate categoriesIndexed array
        $categories = $this->categoryRepository->findAll();
        $this->categoriesIndexed = [];
        foreach ($categories as $category) {
            $this->categoriesIndexed[$category->getId()] = [
                'name' => $category->getName(),
            ];
        }
    }
}
