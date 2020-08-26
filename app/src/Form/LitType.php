<?php

namespace App\Form;

use App\Entity\Literature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('literatureCode')
            ->add('Authors')
            ->add('Year')
            ->add('DOI')
            ->add('Title')
            ->add('where')
            ->add('litlink')
            ->add('lit_note')
            ->add('mamias')
            ->add('country')
            ->add('users');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Literature::class,
        ]);
    }
}
