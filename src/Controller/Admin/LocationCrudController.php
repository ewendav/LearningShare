<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Regex;
use function Symfony\Component\Translation\t;

class LocationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Location::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Lieu'))
            ->setEntityLabelInPlural(t('Lieux'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('adress', t('Adresse'));
        yield TextField::new('zipcode', t('Code postal'))
            ->setFormTypeOption('constraints', [
                new Regex([
                    'pattern' => '/^\d{5}$/',
                    'message' => t('Le code postal doit contenir exactement 5 chiffres.')
                ])
            ]);
        yield TextField::new('city', t('Ville'));
    }
}
