<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exchange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExchangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skillTaughtCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie de compétence enseignée',
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
            ->add('skillTaughtName', TextType::class, [
                'label' => 'Compétence enseignée',
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
            ->add('skillRequestedCategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie de compétence demandée',
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
            ->add('skillRequestedName', TextType::class, [
                'label' => 'Compétence demandée',
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
            ->add('rateId', HiddenType::class, [
                'data' => 1,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exchange::class,
        ]);
    }
}
