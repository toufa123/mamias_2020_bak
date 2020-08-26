<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PathwayAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            //->add('id')
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('VectorName', null, ['label' => 'Pathway'])
            ->add('Certainty', null, ['label' => 'Certainty level of pathway']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('VectorName', null, ['label' => 'Pathway'])
            ->add('Certainty', null, ['label' => 'Certainty level of pathway'])
            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->hasParentFieldDescription()) {
            $formMapper
                //->add('id')
                //->add('mamias', null, ['label' => 'Species Name'])
                ->add('VectorName', null, ['label' => 'Pathway'])
                ->add(
                    'Certainty',
                    ChoiceType::class,
                    [
                        'placeholder' => 'What Certainty ?',
                        'choices' => [
                            'High' => 'High',
                            'Medium' => 'Medium',
                            'Low' => 'Low',
                            'unknown' => 'unknown'
                        ],
                        'required' => false
                    ],
                    ['label' => 'Certainty level of pathway']
                );
        } else {
            $formMapper
                //->add('id')
                ->add('mamias', null, ['label' => 'Species Name'])
                ->add('VectorName', null, ['label' => 'Pathway'])
                ->add(
                    'Certainty',
                    ChoiceType::class,
                    [
                        'choices' => [
                            '' => 'Please Select...',
                            'High' => 'High',
                            'Medium' => 'Medium',
                            'Low' => 'Low',
                        ],
                    ],
                    ['label' => 'Certainty level of pathway', 'required' => false]
                );
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('mamias', ['label' => 'Species Name'])
            ->add('VectorName', ['label' => 'Pathway'])
            ->add('Certainty', ['label' => 'Certainty level of pathway']);
    }
}
