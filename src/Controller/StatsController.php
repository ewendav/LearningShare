<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    public function __construct(private StatsService $statsService) {}

    #[Route('/stats', name: 'Stats')]
    public function index(): Response
    {
        $topSkills = $this->statsService->getTopSkills();
        $mostTaughtSkills = $this->statsService->getMostTaughtSkills();
        $topTeachers = $this->statsService->getTopTeachersLast6Months();

        return $this->render('stats/stats.html.twig', [
            'topSkills' => $topSkills,
            'mostTaughtSkills' => $mostTaughtSkills,
            'topTeachers' => $topTeachers,
        ]);
    }
}
