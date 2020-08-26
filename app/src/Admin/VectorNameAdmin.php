<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use RedCode\TreeBundle\Admin\AbstractTreeAdmin;

final class VectorNameAdmin extends AbstractTreeAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('id')
            ->add('parent', null, ['label' => 'Category'])
            ->add('vectorName', null, ['label' => 'Subcategory'])
            //->add('lft')
            //->add('lvl')
            //->add('rgt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            //->add('id')
            ->add('vectorIcone', null, ['label' => 'Icon'])
            ->add('parent', null, ['label' => 'Category'])
            ->add('vectorName', null, ['label' => 'Subcategory'])
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
            ->add('vectorIcone', null, ['label' => 'Icone'])
            ->add('parent', null, ['label' => 'Category'])
            ->add('vectorName', null, ['label' => 'Subcategory']);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            //->add('id')
            ->add('vectorIcone', null, ['label' => 'Icone'])
            ->add('parent', null, ['label' => 'Category'])
            ->add('vectorName', null, ['label' => 'Subcategory']);
    }
}
