<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatsController extends AbstractController
{
    public function __construct(private StatsService $statsService, private ChartBuilderInterface $chartBuilder) {}

    #[Route('/stats', name: 'Stats')]
    public function index(): Response
    {
        $topSkills = $this->statsService->getTopSkills();
        $mostTaughtSkills = $this->statsService->getMostTaughtSkills();
        $topTeachers = $this->statsService->getTopTeachersLast6Months();

        // Chart 1 : Compétences les plus recherchées
        $searchChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $searchChart->setData([
            'labels' => array_column($topSkills, 'name'),
            'datasets' => [[
                'label' => 'Recherches',
                'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                'data' => array_column($topSkills, 'counter'),
            ]],
        ]);

        // Chart 2 : Compétences les plus enseignées
        $taughtChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $taughtChart->setData([
            'labels' => array_column($mostTaughtSkills, 'skillName'),
            'datasets' => [[
                'label' => 'Sessions',
                'backgroundColor' => 'rgba(153, 102, 255, 0.5)',
                'data' => array_column($mostTaughtSkills, 'usageCount'),
            ]],
        ]);

        // Chart 3 : Top enseignants
        $teacherChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $teacherChart->setData([
            'labels' => array_map(fn($e) => $e['user']->getFirstname(), $topTeachers),
            'datasets' => [[
                'label' => 'Score d’activité',
                'backgroundColor' => 'rgba(255, 159, 64, 0.5)',
                'data' => array_column($topTeachers, 'activityScore'),
            ]],
        ]);

        return $this->render('stats/stats.html.twig', [
            'searchChart' => $searchChart,
            'taughtChart' => $taughtChart,
            'teacherChart' => $teacherChart,
            'topSkills' => $topSkills,
            'mostTaughtSkills' => $mostTaughtSkills,
            'topTeachers' => $topTeachers,
        ]);
    }
    /* public function index(): Response
    {
        $topSkills = $this->statsService->getTopSkills();
        $mostTaughtSkills = $this->statsService->getMostTaughtSkills();
        $topTeachers = $this->statsService->getTopTeachersLast6Months();

        return $this->render('stats/stats.html.twig', [
            'topSkills' => $topSkills,
            'mostTaughtSkills' => $mostTaughtSkills,
            'topTeachers' => $topTeachers,
        ]);
    }*/
}
