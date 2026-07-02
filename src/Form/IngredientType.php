<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'pill-input', 'placeholder' => 'Name (e.g. Sugar)'],
                'constraints' => [new NotBlank()]
            ])
            ->add('baseQuantity', IntegerType::class, [
                'attr' => ['class' => 'pill-input', 'placeholder' => 'Qty (e.g. 500)'],
                'required' => false,
                'constraints' => [new Positive()]
            ])
            ->add('unit', TextType::class, [
                'attr' => ['class' => 'pill-input', 'placeholder' => 'Unit (e.g. g, ml)'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
