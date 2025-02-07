<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\RecipeStep;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [new NotBlank()],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter recipe title'
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [new NotBlank()],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Describe your recipe'
                ]
            ])
            ->add('preparationTime', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Enter preparation time',
                    'type' => 'number'
                ],
                'label' => 'Preparation Time (minutes)',
                'help' => 'Time needed to prepare ingredients'
            ])
            ->add('cookingTime', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Enter cooking time',
                    'type' => 'number'
                ],
                'label' => 'Cooking Time (minutes)',
                'help' => 'Time needed to cook the recipe'
            ])
            ->add('servings', NumberType::class, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Number of servings',
                    'type' => 'number'
                ],
                'label' => 'Number of Servings',
                'help' => 'How many people can this recipe serve?'
            ])
            ->add('difficulty', ChoiceType::class, [
                'choices' => [
                    'Easy' => 'Easy',
                    'Medium' => 'Medium',
                    'Hard' => 'Hard',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('steps', CollectionType::class, [
                'entry_type' => RecipeStepType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__step__',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}