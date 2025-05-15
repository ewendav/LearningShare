<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
#[Route('/change-locale/{locale}', name: 'change_locale')]
public function changeLocale(Request $request, string $locale): RedirectResponse
{
// Stocker la locale en session
$request->getSession()->set('_locale', $locale);

// Rediriger vers la page précédente
$referer = $request->headers->get('referer');
return new RedirectResponse($referer ?: $this->generateUrl('app_home'));
}
}
