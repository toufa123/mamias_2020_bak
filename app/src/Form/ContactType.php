<?php

namespace App\Form;

use App\Entity\Contact;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'FirstName',
                TextType::class,
                ['label' => 'First Name', 'attr' => ['class' => 'form-control col-md-6']]
            )
            ->add(
                'LastName',
                TextType::class,
                ['label' => 'Last Name', 'attr' => ['class' => 'form-control col-md-6']]
            )
            ->add('email', TextType::class, ['label' => 'Email', 'attr' => ['class' => 'form-control']])
            ->add('subject', TextType::class, ['label' => 'Subject', 'attr' => ['class' => 'form-control']])
            ->add('message', TextareaType::class, ['label' => 'message', 'attr' => ['class' => 'form-control']])
            ->add('recaptcha', EWZRecaptchaType::class, array('required' => true,
                'attr' => array(
                    'options' => array(
                        'theme' => 'light',
                        'type' => 'image',
                        'size' => 'normal',
                        'defer' => true,
                        'async' => true
                    )
                ),
                'mapped' => false,
                'constraints' => array(
                    new RecaptchaTrue()
                )
            ))
            ->add('Save', SubmitType::class, ['label' => 'Send', 'attr' => ['class' => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Contact::class,
            ]
        );
    }

    public function getName()
    {
        return 'Serach';
    }
}
