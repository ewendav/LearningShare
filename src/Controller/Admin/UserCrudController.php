<?php
// src/Controller/Admin/UserCrudController.php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('email');
        // Champ plainPassword, non-persisted
        yield TextField::new('plainPassword', 'Mot de passe')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setHelp('Laissez vide pour conserver le mot de passe actuel.');
        yield ImageField::new('avatarPath')
            ->setBasePath('uploads/avatars')
            ->setUploadDir('public/uploads/avatars')
            ->setRequired(false);
        yield TextField::new('firstname');
        yield TextField::new('lastname');
        yield TextEditorField::new('biography')->onlyOnForms();
        yield TextField::new('phone');
        yield NumberField::new('balance');
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
