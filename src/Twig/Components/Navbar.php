<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

#[AsLiveComponent]
final class Navbar
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public bool $darkMode = false;

    public bool $isAdmin = false;

    public function __construct(
        private RequestStack $requestStack,
        private Security $security
    ) {}

    public function mount(): void
    {
        $user = $this->security->getUser();
        $this->isAdmin = $user && in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    #[LiveAction]
    public function toggleDarkMode(): void
    {
        $session = $this->requestStack->getSession();
        $this->darkMode = $session->get('isDarkMode', false);
        $this->darkMode = !$this->darkMode;
        $session->set('isDarkMode', $this->darkMode);
    }
}
