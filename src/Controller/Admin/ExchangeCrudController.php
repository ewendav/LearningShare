<?php

namespace App\Controller\Admin;

use App\Entity\Exchange;
use App\Entity\Session;
use App\Entity\Rate;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ExchangeCrudController extends AbstractCrudController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Exchange::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Échange')
            ->setEntityLabelInPlural('Échanges')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function createEntity(string $entityFqcn)
    {
        $exchange = new Exchange();

        // Pré-remplissage de la session
        $session = new Session();
        $session->setDate(new \DateTime());
        $session->setStartTime(new \DateTime('09:00'));
        $session->setEndTime(new \DateTime('10:00'));
        $session->setDescription('Default Description');

        // ⚡ Correction ici : charger l'objet Rate
        $rate = $this->em->getRepository(Rate::class)->find(2);
        $session->setCost($rate);

        $exchange->setSession($session);

        return $exchange;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        $requester = AssociationField::new('requester', 'Demandeur')
            ->formatValue(fn($value, Exchange $e) => $e->getRequester()?->getFirstname().' '.$e->getRequester()?->getLastname())
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname().' '.$u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $requester = $requester->setFormTypeOption('disabled', true);
        }

        yield $requester;

        yield AssociationField::new('attendee', 'Participant')
            ->formatValue(fn($value, Exchange $e) => $e->getAttendee()?->getFirstname().' '.$e->getAttendee()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        yield AssociationField::new('skillRequested', 'Compétence demandée')
            ->formatValue(fn($value, Exchange $e) => $e->getSkillRequested()?->getName())
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', 'category.name');

        yield AssociationField::new('session.skillTaught', 'Compétence enseignée')
            ->formatValue(fn($value, Exchange $e) => $e->getSession()?->getSkillTaught()?->getName() ?? '')
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', 'category.name');

        yield FormField::addPanel('Session liée');
        yield AssociationField::new('session.cost', 'Coût')
            ->onlyOnForms()
            ->formatValue(fn($value, Exchange $e) => $e->getSession()?->getCost()?->getAmount().' jetons')
            ->setCrudController(RateCrudController::class)
            ->setFormTypeOption('choice_label', function ($rate) {
                return $rate->getAmount().' jetons';
            });
        yield DateField::new('session.date', 'Date');
        yield TimeField::new('session.startTime', 'Heure début');
        yield TimeField::new('session.endTime', 'Heure fin');
        yield TextField::new('session.description', 'Description');


    }
}
