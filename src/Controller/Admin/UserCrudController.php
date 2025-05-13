<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use function Symfony\Component\Translation\t;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('Utilisateur'))
            ->setEntityLabelInPlural(t('Utilisateurs'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('email', t('Email'));

        yield TextField::new('plainPassword', t('Mot de passe'))
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setHelp(t('Laissez vide pour conserver le mot de passe actuel.'));

        yield ImageField::new('avatarPath', t('Avatar'))
            ->setBasePath('uploads/avatars')
            ->setUploadDir('public/uploads/avatars')
            ->setRequired(false);

        yield TextField::new('firstname', t('Prénom'));

        yield TextField::new('lastname', t('Nom'));

        yield TextEditorField::new('biography', t('Biographie'))->onlyOnForms();

        yield TextField::new('phone', t('Téléphone'));

        yield NumberField::new('balance', t('Solde'));

        yield ChoiceField::new('roles', t('Accès'))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
            ])
            ->formatValue(function ($roles, $entity) {
                // Ne garde que les rôles autres que ROLE_USER
                $displayRoles = array_filter($roles, fn($r) => $r !== 'ROLE_USER');

                if (empty($displayRoles)) {
                    return 'Par défaut';
                }

                return implode(', ', $displayRoles);
            });
    }

    public function persistEntity(EntityManagerInterface $em, $user): void
    {
        $this->encodePassword($user);
        parent::persistEntity($em, $user);
    }

    public function updateEntity(EntityManagerInterface $em, $user): void
    {
        $this->encodePassword($user);
        parent::updateEntity($em, $user);
    }

    private function encodePassword(User $user): void
    {
        if ($plain = $user->getPlainPassword()) {
            $hashed = $this->passwordHasher->hashPassword($user, $plain);
            $user->setPassword($hashed);
            $user->setPlainPassword(null);
        }
    }
}
