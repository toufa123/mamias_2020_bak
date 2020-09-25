<?php

namespace App\Form;

use App\Application\Sonata\UserBundle\Entity\User;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(['message' => 'Please enter a valid email address.'])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    //   new NotBlank([
                    //        'message' => 'Please enter a password',
                    //    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        //   max length allowed by Symfony for security reasons
                        'max' => 4096,

                    ]),
                    //new PasswordStrength(['message' => 'password_too_weak', 'minLength' => 8, 'minStrength' => 3]),
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                    //'invalid_message' => 'Your password does not match the confirmation.'
                ],
            ])
            ->add(
                'country',
                null,
                ['required' => true, 'placeholder' => 'Choose a country',]
            );;
    }


    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'csrf_protection' => true,
                // the name of the hidden HTML field that stores the token
                //'csrf_field_name' => '_token',
                // an arbitrary string used to generate the value of the token
                // using a different string for each form improves its security
                //'csrf_token_id' => 'registration_item',
                //'validation_groups' => false
                //'validation_groups' => ['Default', 'registration']
            ]
        );
    }
}
