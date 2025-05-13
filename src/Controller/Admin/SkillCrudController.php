<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Skill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use function Symfony\Component\Translation\t;

class SkillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Skill::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $entity = new $entityFqcn();
        $entity->setSearchCounter(0); // Initialise à 0

        return $entity;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Compétence'))
            ->setEntityLabelInPlural(t('Compétences'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield AssociationField::new('category', t('Catégorie'))
            ->formatValue(fn($value, Skill $e) => $e->getCategory()?->getName() ?? '')
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name');

        yield TextField::new('name', t('Nom'));

        yield IntegerField::new('searchCounter', t('Compteur de recherches'))
            ->setFormTypeOption('disabled', true);
    }
}
