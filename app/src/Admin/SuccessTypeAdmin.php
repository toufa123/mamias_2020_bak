<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class SuccessTypeAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('id')
            ->add('successCode', null, ['label' => 'Code'])
            ->add('successType', null, ['label' => 'Establishement/Success']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('successCode', null, ['label' => 'Code'])
            ->add('successType', null, ['label' => 'Establishement/Success'])
            ->add('successexpl', null, ['label' => 'Explanation'])
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
            ->add('successCode', null, ['label' => 'Code'])
            ->add('successType', null, ['label' => 'Establishement/Success'])
            ->add('successexpl', null, ['label' => 'Explanation']);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            //->add('id')
            ->add('successCode', null, ['label' => 'Code'])
            ->add('successType', null, ['label' => 'Establishement/Success'])
            ->add('successexpl', null, ['label' => 'Explanation']);
    }
}
