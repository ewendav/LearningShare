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
    public function __construct(
        private StatsService $statsService,
        private ChartBuilderInterface $chartBuilder
    ) {}

    #[Route('/stats', name: 'Stats')]
    public function index(): Response
    {
        $topSkills = $this->statsService->getTopSkills();
        $mostTaughtSkills = $this->statsService->getMostTaughtSkills();
        $topTeachers = $this->statsService->getTopTeachersLast6Months();

        // Chart 1 : Compétences les plus recherchées (barres horizontales)
        $searchChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $searchChart->setData([
            'labels' => array_column($topSkills, 'name'),
            'datasets' => [[
                'label' => 'Recherches',
                'data' => array_column($topSkills, 'counter'),
                'backgroundColor' => 'rgba(76, 180, 157, 0.6)',
                'borderColor' => 'rgba(76, 180, 157, 1)',
                'borderWidth' => 1,
            ]],
        ]);
        $searchChart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false,
                ]
            ],
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'display' => false, // Supprime le quadrillage vertical
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false, // Supprime le quadrillage horizontal
                    ],
                ],
            ],
        ]);

        // Chart 2 : Compétences les plus enseignées (barres horizontales)
        $taughtChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $taughtChart->setData([
            'labels' => array_column($mostTaughtSkills, 'skillName'),
            'datasets' => [[
                'label' => 'Sessions',
                'data' => array_column($mostTaughtSkills, 'usageCount'),
                'backgroundColor' => 'rgba(76, 180, 157, 0.6)',
                'borderColor' => 'rgba(76, 180, 157, 1)',
                'borderWidth' => 1,
            ]],
        ]);
        $taughtChart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false,
                ]
            ],
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'display' => false, // Supprime le quadrillage vertical
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false, // Supprime le quadrillage horizontal
                    ],
                ],
            ],
        ]);

        // Chart 3 : Top enseignants (barres verticales)
        $teacherChart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $teacherChart->setData([
            'labels' => array_map(fn($e) => $e['user']->getFirstname(), $topTeachers),
            'datasets' => [[
                'label' => 'Score d’activité',
                'data' => array_column($topTeachers, 'activityScore'),
                'backgroundColor' => 'rgba(255, 159, 64, 0.6)',
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'borderWidth' => 1,
            ]],
        ]);
        $teacherChart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false,
                ]
            ],
            'indexAxis' => 'x',
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'display' => false, // Supprime le quadrillage vertical
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false, // Supprime le quadrillage horizontal
                    ],
                ],
            ],
        ]);

        //Chart 4 : Sessions par mois sur 12 mois
        $monthlyData = $this->statsService->getMonthlySessionsData();

        $labels = array_keys($monthlyData);
        $lessonData = array_column($monthlyData, 'lessons');
        $exchangeData = array_column($monthlyData, 'exchanges');
        $totalData = array_map(fn($d) => $d['lessons'] + $d['exchanges'], $monthlyData);

        $monthlyChart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $monthlyChart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Cours',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'data' => $lessonData,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Échanges',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                    'data' => $exchangeData,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Total',
                    'borderColor' => 'rgba(76, 180, 157, 1)',
                    'backgroundColor' => 'rgba(76, 180, 157, 0.2)',
                    'data' => $totalData,
                    'tension' => 0.3,
                ],
            ],
        ]);


        return $this->render('stats/stats.html.twig', [
            'searchChart' => $searchChart,
            'taughtChart' => $taughtChart,
            'teacherChart' => $teacherChart,
            'monthlyChart' => $monthlyChart,
        ]);
    }
}
