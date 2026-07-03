<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please select a rating.']),
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'Rating must be between 1 and 5.',
                    ]),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                    'step' => '0.1',
                    'class' => 'pill-input',
                ],
                'html5' => true,
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your comment.']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Your comment must be at least {{ limit }} characters long.',
                    ]),
                ],
                'attr' => [
                    'class' => 'pill-input',
                    'placeholder' => 'Write your cooking feedback or review here...',
                    'rows' => 4,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
