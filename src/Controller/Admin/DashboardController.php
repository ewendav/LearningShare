<?php

namespace App\Controller\Admin;

use App\Entity\Blacklist;
use App\Entity\Category;
use App\Entity\Exchange;
use App\Entity\Lesson;
use App\Entity\Location;
use App\Entity\Rate;
use App\Entity\Report;
use App\Entity\Review;
use App\Entity\Role;
use App\Entity\Session;
use App\Entity\Skill;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {

        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        return $this->redirectToRoute('admin_category_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LearningShare Administration');
    }

    public function configureMenuItems(): iterable
    {
        return[

            // permet de grouper les elements qui seront affiché sur le dashboard
        MenuItem::section('Gestion'),
        MenuItem::linkToCrud('Utilisateurs','fa fa-user',User::class),
        MenuItem::linkToCrud('Echanges', 'fa fa-exchange', Exchange::class),
        MenuItem::linkToCrud('Cours', 'fa fa-book', Lesson::class),
        MenuItem::linkToCrud('Catégories', 'fa fa-list', Category::class),
        MenuItem::linkToCrud('Compétences', 'fa fa-cogs', Skill::class),
        MenuItem::linkToCrud('Adresses', 'fa fa-map-marker', Location::class),
        MenuItem::linkToCrud('Rate', 'fa fa-star', Rate::class),
        MenuItem::linkToCrud('Report', 'fa fa-flag', Report::class),
        MenuItem::linkToCrud('Review', 'fa fa-comment', Review::class),
        MenuItem::linkToCrud('Mot Blacklist', 'fa fa-ban', Blacklist::class),
        MenuItem::linkToCrud('Role', 'fa fa-user-tag', Role::class),
        ];
    }
}
