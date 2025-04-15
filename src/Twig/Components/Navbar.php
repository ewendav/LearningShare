<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsLiveComponent]
final class Navbar
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public bool $darkMode = false;


    public function __construct(private RequestStack $requestStack,)
    {
    }

    public function mount(): void
    {
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
