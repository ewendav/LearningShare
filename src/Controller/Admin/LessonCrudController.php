<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Entity\Location;
use App\Entity\User;
use App\Entity\Session;
use App\Entity\Rate;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use function Symfony\Component\Translation\t;

class LessonCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $em) {}

    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Lesson'))
            ->setEntityLabelInPlural(t('Lessons'))
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function createEntity(string $entityFqcn)
    {
        $lesson = new Lesson();

        $session = new Session();
        $session->setDate(new \DateTime());
        $session->setStartTime(new \DateTime('09:00'));
        $session->setEndTime(new \DateTime('10:00'));
        $session->setDescription('Default Description');

        $rate = $this->em->getRepository(Rate::class)->find(2);
        $session->setCost($rate);

        $lesson->setSession($session);

        return $lesson;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        $host = AssociationField::new('host', t('Host'))
            ->setFormTypeOption('choice_label', fn($user) => $user->getFirstname().' '.$user->getLastname())
            ->setCrudController(UserCrudController::class)
            ->formatValue(fn($value, Lesson $lesson) => $lesson->getHost()->getFirstname().' '.$lesson->getHost()->getLastname());

        if ($pageName !== Crud::PAGE_NEW) {
            $host = $host->setFormTypeOption('disabled', true);
        }

        yield $host;

        yield IntegerField::new('maxAttendees', t('Maximum number of participants'))
            ->onlyOnForms()
            ->setHelp(t('The maximum number of participants for this lesson.'));

        yield AssociationField::new('attendees', t('Participants'))
            ->setFormTypeOption('multiple', true)
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('choice_label', fn($user) => $user->getFirstname().' '.$user->getLastname())
            ->formatValue(fn($value, Lesson $lesson) =>
            implode(', ', array_map(fn($u) =>
                $u->getFirstname().' '.$u->getLastname(), $lesson->getAttendees()->toArray()))
            );

        yield AssociationField::new('location', t('Location'))
            ->setCrudController(LocationCrudController::class)
            ->formatValue(function ($value, Lesson $lesson) {
                if ($lesson->getLocation()) {
                    $location = $lesson->getLocation();
                    $location->__load();
                    return $location->getAdress() . ', ' . $location->getZipCode() . ' ' . $location->getCity();
                }
                return 'N/A';
            })
            ->onlyOnIndex();

        yield AssociationField::new('location', t('Location'))
            ->setCrudController(LocationCrudController::class)
            ->onlyOnForms()
            ->setRequired(true)
            ->setFormTypeOption('choice_label', function(Location $location) {
                return $location->getAdress() . ', ' . $location->getZipCode() . ' ' . $location->getCity();
            })
            ->setQueryBuilder(fn($qb) => $qb->orderBy('entity.city', 'ASC'));

        yield AssociationField::new('session.skillTaught', t('Taught Skill'))
            ->formatValue(fn($value, Lesson $lesson) => $lesson->getSession()?->getSkillTaught()?->getName() ?? '')
            ->setRequired(true)
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', 'category.name');

        yield FormField::addPanel(t('Linked Session'));

        yield AssociationField::new('session.cost', t('Cost'))
            ->onlyOnForms()
            ->formatValue(fn($value, Lesson $lesson) => $lesson->getSession()?->getCost()?->getAmount().' jetons')
            ->setCrudController(RateCrudController::class)
            ->setFormTypeOption('choice_label', fn($rate) => $rate->getAmount().' jetons');

        yield DateField::new('session.date', t('Date'))
            ->formatValue(fn($value) => $value ? $value->format('Y-m-d') : '');

        yield TimeField::new('session.startTime', t('Start Time'));
        yield TimeField::new('session.endTime', t('End Time'));
        yield TextField::new('session.description', t('Description'));
    }
}
