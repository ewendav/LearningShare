<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

class ReportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Report::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Report'))
            ->setEntityLabelInPlural(t('Reports'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        $giver = AssociationField::new('ReportGiver', t('By'))
            ->formatValue(fn($value, Report $e) => $e->getReportGiver()?->getFirstname() . ' ' . $e->getReportGiver()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $giver = $giver->setFormTypeOption('disabled', true);
        }
        yield $giver;

        $receiver = AssociationField::new('reportReceiver', t('Against'))
            ->formatValue(fn($value, Report $e) => $e->getReportReceiver()?->getFirstname() . ' ' . $e->getReportReceiver()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $receiver = $receiver->setFormTypeOption('disabled', true);
        }
        yield $receiver;

        yield TextField::new('content',t('Content'));
    }
}
