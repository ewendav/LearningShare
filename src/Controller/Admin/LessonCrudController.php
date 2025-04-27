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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField; // Import du champ IntegerField

class LessonCrudController extends AbstractCrudController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Cours')
            ->setEntityLabelInPlural('Cours')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function createEntity(string $entityFqcn)
    {
        $lesson = new Lesson();

        // Pré-remplissage de la session
        $session = new Session();
        $session->setDate(new \DateTime());
        $session->setStartTime(new \DateTime('09:00'));
        $session->setEndTime(new \DateTime('10:00'));
        $session->setDescription('Default Description');

        // ⚡ Correction ici : charger l'objet Rate
        $rate = $this->em->getRepository(Rate::class)->find(2);
        $session->setCost($rate);

        $lesson->setSession($session);

        return $lesson;
    }

    public function configureFields(string $pageName): iterable
    {
        // ID uniquement en liste
        yield IdField::new('id')->onlyOnIndex();

        // Animateur (Host) : afficher Prénom et Nom de l'animateur
        yield AssociationField::new('host', 'Animateur')
            ->setFormTypeOption('choice_label', fn($user) => $user->getFirstname().' '.$user->getLastname())
            ->setCrudController(UserCrudController::class)
            ->formatValue(fn($value, Lesson $lesson) => $lesson->getHost()->getFirstname().' '.$lesson->getHost()->getLastname());

        // Nombre maximal de participants (maxAttendees)
        yield IntegerField::new('maxAttendees', 'Nombre maximal de participants')
            ->onlyOnForms()
            ->setHelp('Le nombre maximum de participants pour cette leçon.'); // Optionnel : ajouter un texte d'aide

        // Participants inscrits (attendees) : afficher Prénom et Nom
        yield AssociationField::new('attendees', 'Participants')
            ->setFormTypeOption('multiple', true)
            ->setCrudController(UserCrudController::class)
            ->setFormTypeOption('choice_label', fn($user) => $user->getFirstname().' '.$user->getLastname())
            ->formatValue(fn($value, Lesson $lesson) => implode(', ', array_map(fn($u) => $u->getFirstname().' '.$u->getLastname(), $lesson->getAttendees()->toArray())));

        // Lieu (Location) : Afficher l'adresse complète dans la liste
        yield AssociationField::new('location', 'Lieu')
            ->setCrudController(LocationCrudController::class)
            ->formatValue(function ($value, Lesson $lesson) {
                if ($lesson->getLocation()) {
                    $location = $lesson->getLocation();
                    $location->__load(); // Force le chargement de la location
                    return $location->getAdress() . ', ' . $location->getZipCode() . ' ' . $location->getCity(); // Afficher l'adresse complète
                }
                return 'N/A'; // Si pas de location
            })
            ->onlyOnIndex();  // Afficher ce champ uniquement dans la liste (view)

        // Lieu (Location) : Afficher l'adresse complète dans le formulaire
        yield AssociationField::new('location', 'Lieu')
            ->setCrudController(LocationCrudController::class)
            ->onlyOnForms()  // Afficher ce champ uniquement dans le formulaire
            ->setRequired(true)  // Le champ est obligatoire
            ->setFormTypeOption('choice_label', function(Location $location) {
                // Concaténer les champs pour afficher l'adresse complète
                return $location->getAdress() . ', ' . $location->getZipCode() . ' ' . $location->getCity();
            })
            ->setQueryBuilder(function ($queryBuilder) {
                return $queryBuilder->orderBy('entity.city', 'ASC'); // Tri par ville
            });

        // Compétence enseignée
        yield AssociationField::new('session.skillTaught', 'Compétence enseignée')
            ->formatValue(fn($value, Lesson $lesson) => $lesson->getSession()?->getSkillTaught()?->getName() ?? '')
            ->setRequired(true)
            ->setCrudController(SkillCrudController::class)
            ->setFormTypeOption('choice_label', 'name')
            ->setFormTypeOption('group_by', 'category.name');

        // Coût via la session liée
        yield FormField::addPanel('Session liée');
        yield AssociationField::new('session.cost', 'Coût')
            ->onlyOnForms()
            ->formatValue(fn($value, Lesson $lesson) => $lesson->getSession()?->getCost()?->getAmount().' jetons')
            ->setCrudController(RateCrudController::class)
            ->setFormTypeOption('choice_label', function ($rate) {
                return $rate->getAmount().' jetons';
            });

        // Informations de session : date, heure début, heure fin, description
        yield DateField::new('session.date', 'Date')
            ->formatValue(fn($value) => $value ? $value->format('Y-m-d') : '');
        yield TimeField::new('session.startTime', 'Heure début');
        yield TimeField::new('session.endTime', 'Heure fin');
        yield TextField::new('session.description', 'Description');
    }
}
