<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class CountryAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('id')
            ->add('countryCode', null, ['label' => 'Country Code'])
            ->add('country', null, ['label' => 'Country Name'])
            ->add('latitude', null, ['label' => 'latitude'])
            ->add('longitude', null, ['label' => 'longitude']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            //->add('id')
            ->add('countryCode', null, ['label' => 'Country Code'])
            ->add('country', null, ['label' => 'Country Name'])
            ->add('latitude', null, ['label' => 'latitude'])
            ->add('longitude', null, ['label' => 'longitude'])
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
            ->add('countryCode', null, ['label' => 'Country Code'])
            ->add('country', null, ['label' => 'Country Name'])
            ->add('latitude', null, ['label' => 'latitude'])
            ->add('longitude', null, ['label' => 'longitude']);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            //->add('id')
            ->add('countryCode', null, ['label' => 'Country Code'])
            ->add('country', null, ['label' => 'Country Name'])
            ->add('latitude', null, ['label' => 'latitude'])
            ->add('longitude', null, ['label' => 'longitude']);
    }
}
