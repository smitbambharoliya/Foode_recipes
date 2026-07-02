<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Full Name',
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter your full name'
                    ),
                ]
            ])
            ->add('email',EmailType::class,[
                'label' => 'Email Address',
                'required' => true,
                'attr' => ['autocomplete '=>'email'],
                'trim' => true,
                'mapped' =>true,
            ])
            ->add('phone', IntegerType::class,[
                'label'  => 'Phone Number',
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter your phone number.',
                    )
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'required'=>true,
                'label' => 'Gender',
                'choices'=> [
                    'Male' => 'male',
                    'Female' => 'female',
                    'Other' => 'other',
                ],
                'expanded' => true,
                'multiple' => false,

                'row_attr' =>['class' => 'gender-selaction'],
            ])
            ->add('age',IntegerType::class, [
                'required'=>true,
                'label' => 'Age',
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter your age',
                    ),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a city',
                    ),
                ],
            ])
            ->add('state',ChoiceType::class, [
                'label' => 'selact_state',
                'attr' => [
                    'class' => 'serchable-state-selection',
                    'placeholder' => 'select state',
                ],
                'choices' => [
                    'Andaman and Nicobar Islands' => 'AN',
                    'Arunachal Pradesh' => 'AR',
                    'Assam' => 'AS',
                    'Bihar' => 'BR',
                    'Chandigarh' => 'CH',
                    'Chhattisgarh' => 'CT',
                    'Dadra and Nagar Haveli and Daman and Diu' => 'DD',
                    'Delhi' => 'DL',
                    'Goa' => 'GA',
                    'Gujarat' => 'GJ',
                    'Haryana' => 'HR',
                    'Himachal Pradesh' => 'HP',
                    'Jammu and Kashmir' => 'JK',
                    'Jharkhand' => 'JH',
                    'Karnataka' => 'KA',
                    'Kerala' => 'KL',
                    'Ladakh' => 'LA',
                    'Lakshadweep' => 'LD',
                    'Madhya Pradesh' => 'MP',
                    'Maharashtra' => 'MH',
                    'Manipur' => 'MN',
                    'Meghalaya' => 'ML',
                    'Mizoram' => 'MZ',
                    'Nagaland' => 'NL',
                    'Odisha' => 'OR',
                    'Puducherry' => 'PY',
                    'Punjab' => 'PB',
                    'Sikkim' => 'SK',
                    'Rajasthan' => 'RJ',
                    'Tamil Nadu' => 'TN',
                    'Telangana' => 'TG',
                    'Tripura' => 'TR',
                    'Uttar Pradesh' => 'UP',
                    'Uttarakhand' => 'UT',
                    'West Bengal' => 'WB',
                    'Andhra Pradesh' => 'AP',
                ],
            ])
            ->add('country',CountryType::class,[
                'label' => 'Country name',
                'placeholder' => 'Select country',
                'required'=>true,
                'constraints' => [
                    new NotBlank(
                        message: 'Please select a country',
                    ),
                ],
                'preferred_choices' => ['IN', 'US', 'GB'],
                'attr' => [
                    'class' => 'select2-enable'],

            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.',
                    ),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
