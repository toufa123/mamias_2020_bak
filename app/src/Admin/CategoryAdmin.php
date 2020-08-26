<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;

final class CategoryAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('description')
            ->add('enabled')
            ->add('createdAt');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('description')
            ->add('enabled')
            ->add('createdAt')
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
        $formMapper
            //->add ('id')
            ->add('name')
            ->add('slug')
            ->add('description')
            ->add('enabled')
            ->add('createdAt', DatePickerType::class,
                ['label' => 'Created At'],
                [
                    'dp_side_by_side' => true,
                    'dp_use_current' => true,
                    'dp_collapse' => true,
                    'dp_calendar_weeks' => false,
                    'dp_view_mode' => 'days',
                    'dp_min_view_mode' => 'days',
                ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('description')
            ->add('enabled')
            ->add('createdAt');
    }
}
