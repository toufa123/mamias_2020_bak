<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class EcApAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('id')
            ->add('code', null, ['label' => 'Code'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('code', null, ['label' => 'Code'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion'])
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

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('id')
            ->add('code', null, ['label' => 'Code'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion']);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('code', null, ['label' => 'Code'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion']);
    }
}
