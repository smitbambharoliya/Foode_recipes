<?php

namespace App\Form;

use App\DTO\RecipeInputDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotNull;

class RecipeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a recipe title']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Your title should be at least {{ limit }} characters',
                        'maxMessage' => 'Your title cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('instructions', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Please provide cooking instructions']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Instructions should be at least {{ limit }} characters long',
                    ]),
                ],
            ])
            ->add('baseServings', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please specify the number of servings']),
                    new Type(['type' => 'integer', 'message' => 'The value {{ value }} is not a valid integer.']),
                    new Positive(['message' => 'Servings must be a positive number']),
                    new LessThanOrEqual([
                        'value' => 100,
                        'message' => 'Servings cannot exceed {{ limit }}',
                    ]),
                ],
                'attr' => ['class' => 'pill-input', 'placeholder' => 'e.g. 4']
            ])
            ->add('ingredients', CollectionType::class, [
                'entry_type' => IngredientType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('regionName', TextType::class, [
                'label' => 'Region / Cuisine',
                'required' => false,
                'attr' => [
                    'class' => 'pill-input',
                    'placeholder' => 'e.g. Indian, Italian, Mexican...',
                    'list' => 'region-list',
                    'autocomplete' => 'off'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide a region or cuisine']),
                ],
            ])
            ->add('mealtype', ChoiceType::class, [
                'label' => 'Meal Type',
                'choices'  => [
                    'Breakfast' => 'Breakfast',
                    'Lunch' => 'Lunch',
                    'Dinner' => 'Dinner',
                    'Snack' => 'Snack',
                    'Dessert' => 'Dessert',
                    'Beverage' => 'Beverage',
                ],
                'attr' => ['class' => 'pill-input'],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a meal type']),
                ],
            ])
            ->add('isVeg', ChoiceType::class, [
                'label' => 'Is Veg?',
                'choices'  => [
                    'Veg' => true,
                    'Non-Veg' => false,
                ],
                'attr' => ['class' => 'pill-input'],
                'constraints' => [
                    new NotNull(['message' => 'Please select if the recipe is veg or non-veg']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Recipe Image (JPG/PNG)',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeInputDTO::class,
        ]);
    }
}
