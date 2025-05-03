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
            ->setEntityLabelInSingular(t('User'))
            ->setEntityLabelInPlural(t('Users'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('email', t('Email'));

        yield TextField::new('plainPassword', t('Password'))
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setHelp(t('Leave blank to keep the current password.'));

        yield ImageField::new('avatarPath', t('Avatar'))
            ->setBasePath('uploads/avatars')
            ->setUploadDir('public/uploads/avatars')
            ->setRequired(false);

        yield TextField::new('firstname', t('First name'));

        yield TextField::new('lastname', t('Last name'));

        yield TextEditorField::new('biography', t('Biography'))->onlyOnForms();

        yield TextField::new('phone', t('Phone'));

        yield NumberField::new('balance', t('Balance'));

        yield ChoiceField::new('roles', t('Access'))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->setChoices([
                'Admin' => 'ROLE_ADMIN',
            ])
            ->formatValue(function ($roles, $entity) {
                // Ne garde que les rôles autres que ROLE_USER
                $displayRoles = array_filter($roles, fn($r) => $r !== 'ROLE_USER');

                if (empty($displayRoles)) {
                    return 'Par défaut';
                }

                // Si un ou plusieurs rôles restants
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
