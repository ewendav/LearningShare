<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
            ->setEntityLabelInSingular(t('Avis'))
            ->setEntityLabelInPlural(t('Avis'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        $giver = AssociationField::new('reviewGiver', t('Par'))
            ->formatValue(fn($value, Review $e) => $e->getReviewGiver()?->getFirstname() . ' ' . $e->getReviewGiver()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $giver = $giver->setFormTypeOption('disabled', true);
        }
        yield $giver;

        $receiver = AssociationField::new('reviewReceiver', t('A propos'))
            ->formatValue(fn($value, Review $e) => $e->getReviewReceiver()?->getFirstname() . ' ' . $e->getReviewReceiver()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $receiver = $receiver->setFormTypeOption('disabled', true);
        }
        yield $receiver;

        yield TextField::new('content', t('Contenu'));

        yield IntegerField::new('rating', t('Note'))
            ->setFormTypeOption('attr', ['min' => 1, 'max' => 5]);
    }
}
