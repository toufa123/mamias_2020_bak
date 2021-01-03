<?php

namespace App\Form;

use App\Application\Sonata\UserBundle\Entity\User;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);
        $builder
            ->add('username', null, ['label' => 'Username'])
            //->add('plainPassword', Null, ['label' => 'Current Password'])
            ->add('firstname', null, ['label' => 'First Name'])
            ->add('lastname', null, ['label' => 'Last Name'])
            ->add(
                'dateOfBirth',
                DateType::class,
                ['widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'label' => 'Birth Date', 'required' => false]
            )
            ->add(
                'gender',
                ChoiceType::class,
                [
                    'label' => 'Gender',
                    'choices' => [
                        'unknown' => 'u',
                        'female' => 'f',
                        'male' => 'm',
                    ], 'required' => false
                ]
            )
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => false])
            ->add('phone', TelType::class, ['label' => 'Phone', 'required' => false])
            ->add('skype', null, ['label' => 'Skype', 'required' => false])
            ->add('biography', TextType::class, ['label' => 'Biography', 'required' => false])
            ->add('facebookUid', TextType::class, ['label' => 'Facebook', 'required' => false])
            ->add('twitterUid', TextType::class, ['label' => 'Twitter', 'required' => false])
            ->add('country', null, ['label' => 'Country'])
            ->add('Eco', null, ['label' => 'Ecofunctional Group', 'required' => false])
            ->add('soi', null, ['label' => 'Species of Interest', 'required' => false])
            //->add('literature', CollectionType::class, ['label' => 'Literature'],  array('type' => LiteratureType::class,
            //  'allow_add' => true, 'allow_delete' => true,'prototype' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }

    public function getName()
    {
        return 'fos_user_profile';
    }
}
