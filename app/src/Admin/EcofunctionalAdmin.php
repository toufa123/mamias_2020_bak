<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class EcofunctionalAdmin extends AbstractAdmin
{

    protected $baseRouteName = 'EcoFunctionlGroup';
    protected $perPageOptions = [10, 20, 50, 100, 'All'];
    protected $maxPerPage = '20';
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'ecofunctional',
        '_per_page' => '20',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('ecofunctionalCode', null, ['label' => 'Code'])
            ->add('ecofunctional', null, ['label' => 'EcoFunctional Group']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('ecofunctionalCode', null, ['label' => 'Code'])
            ->add('ecofunctional', null, ['label' => 'EcoFunctional Group'])
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
            ->add('ecofunctionalCode', null, ['label' => 'Code'])
            ->add('ecofunctional', null, ['label' => 'EcoFunctional Group']);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('ecofunctionalCode', null, ['label' => 'Code'])
            ->add('ecofunctional', null, ['label' => 'EcoFunctional Group']);
    }
}
