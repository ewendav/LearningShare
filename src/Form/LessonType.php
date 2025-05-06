<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Lesson;
use App\Entity\Location;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skillCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une catégorie',
                    ]),
                ],
                'attr' => [
                    'class' => 'input-goofy',
                ],
            ])
            ->add('skillName', TextType::class, [
                'label' => 'Compétence',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une compétence',
                    ]),
                ],
                'attr' => [
                    'class' => 'input-goofy',
                    'placeholder' => 'Sélectionnez une compétence',
                ],
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input-goofy',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une date',
                    ]),
                ],
            ])
            ->add('startTime', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input-goofy',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une heure de début',
                    ]),
                ],
            ])
            ->add('endTime', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input-goofy',
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une heure de fin',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse',
                    ]),
                ],
                'attr' => [
                    'class' => 'input-goofy',
                    'placeholder' => 'Entrez l\'adresse',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une ville',
                    ]),
                ],
                'attr' => [
                    'class' => 'input-goofy',
                    'placeholder' => 'Entrez la ville',
                ],
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un code postal',
                    ]),
                ],
                'attr' => [
                    'class' => 'input-goofy',
                    'placeholder' => 'Entrez le code postal',
                ],
            ])
            ->add('maxAttendees', IntegerType::class, [
                'label' => 'Nombre maximum de participants',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nombre de participants',
                    ]),
                    new Positive([
                        'message' => 'Le nombre de participants doit être positif',
                    ]),
                ],
                'attr' => [
                    'class' => 'input-goofy',
                    'placeholder' => 'Sélectionnez le nombre maximum de participants',
                    'min' => 1,
                ],
            ])
            ->add('rateId', HiddenType::class, [
                'data' => 1,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
