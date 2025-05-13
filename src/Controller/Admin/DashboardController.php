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
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\Translation\t;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_user_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/assets/img/logo.png" class="img-fluid d-block mx-auto" style="max-width:100px; width:100%;"><h3 style="text-align: center">LearningShare Administration</h3>')
            ->renderContentMaximized();
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getFirstName() . ' ' . $user->getLastName())
            ->setAvatarUrl('/uploads/avatars/' . $user->getAvatarPath())
            ->displayUserName(true)
            ->addMenuItems([
                MenuItem::linkToRoute(t('My profile'), 'fa fa-id-card', '...', ['...' => '...']),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section(t('Management')),
            MenuItem::linkToCrud(t('Users'), 'fa fa-user', User::class),
            MenuItem::linkToCrud(t('Exchanges'), 'fa fa-exchange', Exchange::class),
            MenuItem::linkToCrud(t('Lessons'), 'fa fa-book', Lesson::class),
            MenuItem::linkToCrud(t('Categories'), 'fa fa-list', Category::class),
            MenuItem::linkToCrud(t('Skills'), 'fa fa-cogs', Skill::class),
            MenuItem::linkToCrud(t('Addresses'), 'fa fa-map-marker', Location::class),
            MenuItem::linkToCrud(t('Rates'), 'fa fa-star', Rate::class),
            MenuItem::linkToCrud(t('Reports'), 'fa fa-flag', Report::class),
            MenuItem::linkToCrud(t('Reviews'), 'fa fa-comment', Review::class),
            MenuItem::linkToCrud(t('Blacklisted Words'), 'fa fa-ban', Blacklist::class),
        ];
    }
}
