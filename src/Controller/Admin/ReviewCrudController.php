<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

class ReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Review'))
            ->setEntityLabelInPlural(t('Reviews'));
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        $giver = AssociationField::new('reviewGiver', t('By'))
            ->formatValue(fn($value, Review $e) => $e->getReviewGiver()?->getFirstname() . ' ' . $e->getReviewGiver()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $giver = $giver->setFormTypeOption('disabled', true);
        }
        yield $giver;

        /* QUAND AVIS SERA SUR USER
        $receiver = AssociationField::new('about', t('About'))
            ->formatValue(fn($value, Review $e) => $e->getAbout()?->getFirstname() . ' ' . $e->getAbout()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $receiver = $receiver->setFormTypeOption('disabled', true);
        }
        yield $receiver;
        */

        yield TextField::new('content',t('Content'));
    }
}
