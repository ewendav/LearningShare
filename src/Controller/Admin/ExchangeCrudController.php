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
use function Symfony\Component\Translation\t;

class ExchangeCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public static function getEntityFqcn(): string
    {
        return Exchange::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Exchange'))
            ->setEntityLabelInPlural(t('Exchanges'));
    }

    public function createEntity(string $entityFqcn)
    {
        $exchange = new Exchange();

        $session = new Session();
        $session->setDate(new \DateTime());
        $session->setStartTime(new \DateTime('09:00'));
        $session->setEndTime(new \DateTime('10:00'));
        $session->setDescription('Default Description');

        $rate = $this->em->getRepository(Rate::class)->find(2);
        $session->setCost($rate);

        $exchange->setSession($session);

        return $exchange;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        $requester = AssociationField::new('requester', t('Requester'))
            ->formatValue(fn($value, Exchange $e) => $e->getRequester()?->getFirstname() . ' ' . $e->getRequester()?->getLastname())
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $requester = $requester->setFormTypeOption('disabled', true);
        }

        yield $requester;

        yield AssociationField::new('attendee', t('Attendee'))
            ->formatValue(fn($value, Exchange $e) => $e->getAttendee()?->getFirstname() . ' ' . $e->getAttendee()?->getLastname())
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('required', false)
            ->setFormTypeOption('choice_label', fn($u) => $u->getFirstname() . ' ' . $u->getLastname());

        yield AssociationField::new('skillRequested', t('Requested Skill'))
            ->formatValue(fn($value, Exchange $e) => $e->getSkillRequested()?->getName())
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', 'category.name');

        yield AssociationField::new('session.skillTaught', t('Taught Skill'))
            ->formatValue(fn($value, Exchange $e) => $e->getSession()?->getSkillTaught()?->getName() ?? '')
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', 'category.name');

        yield FormField::addPanel(t('Linked Session'));

        yield AssociationField::new('session.cost', t('Cost'))
            ->onlyOnForms()
            ->formatValue(fn($value, Exchange $e) => $e->getSession()?->getCost()?->getAmount() . ' jetons')
            ->setCrudController(RateCrudController::class)
            ->setFormTypeOption('choice_label', fn($rate) => $rate->getAmount() . ' jetons');

        yield DateField::new('session.date', t('Date'));
        yield TimeField::new('session.startTime', t('Start Time'));
        yield TimeField::new('session.endTime', t('End Time'));
        yield TextField::new('session.description', t('Description'));
    }
}
