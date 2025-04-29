<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: "/", name: "home")]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

    // Check if 'isDarkMode' is set in the session; if not, set it to false
        if (!$session->has('isDarkMode')) {
            $session->set('isDarkMode', false);
        }



        return $this->render('ecrans/publicSessions.html.twig', [
          'title' => 'test',
        ]);
    }
}
