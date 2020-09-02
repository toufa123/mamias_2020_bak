<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class CountryDistributionAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {

        parent::configureRoutes($collection);
        $collection->add('importNational');

    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['import'] = array(
            'label' => 'Import',
            'url' => $this->generateUrl('importNational'),
            'icon' => 'upload',
            'template' => 'SonataAdminBundle:CRUD:dashboard__action.html.twig', // optional
        );

        return $actions;
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            //->add('id')
            ->add('country', null, ['label' => 'Country'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion'])
            ->add('regionalSea', null, ['label' => 'Regional Seas'])
            ->add('AreaSighting', null, ['label' => '1st Areas Sighting'])
            ->add('nationalstatus', null, ['label' => 'Status of the Species'])
            ->add('areaSuccess', null, ['label' => 'Establishement'])
            ->add('VectorName', null, ['label' => 'Pathways'])
            //->add('areaNote',null, array('label' => 'Note'))
            //->add('createdAt', null, array('label' => 'Created At'))
            ->add('updatedAt', null, ['label' => 'updated At']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', IntegerType::class, ['label' => 'ID', 'header_style' => 'width: 3%'])
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('country', null, ['label' => 'Country'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion'])
            ->add('regionalSea', null, ['label' => 'Regional Seas'])
            ->add('AreaSighting', IntegerType::class, ['label' => '1st Areas Sighting'])
            ->add('nationalstatus', null, ['label' => 'Status of the Species'])
            ->add('areaSuccess', null, ['label' => 'Establishement'])
            //->add('vector', null, array('l)abel' => 'Vector/Pathways'))
            ->add(
                'status',
                ChoiceType::class,
                [
                    'editable' => true,
                    'choices' => [
                        'Validated' => 'Validated',
                        'Non Validated' => 'Non Validated',
                        'Information requested' => 'Information requested'
                    ],
                    'label' => 'Data Status',
                ]
            )
            //->add('areaNote',null, array('label' => 'Note'))
            ->add('createdAt', null, ['label' => 'Created At'])
            //->add('updatedAt', null, array('label' => 'updated At'))
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
                ->with('Species Details', ['class' => 'col-md-6'])
                ->add('country', null, ['label' => 'Country'])
                ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion'])
                ->add('regionalSea', null, ['label' => 'Regional Seas'])
                ->add('VectorName', null, ['label' => 'Pathways'])
                ->add('Certainty', ChoiceType::class,
                    [
                        'choices' => [
                            'Low' => 'Low',
                            'Medium' => 'Medium',
                            'High' => 'High',
                        ],
                        'label' => 'Certainty', 'placeholder' => 'Choose an option', 'required' => false
                    ])
                ->add('AreaSighting', null, ['label' => 'National Sighting'])
                ->add('fisrtSighting', null, ['label' => '1st Sighting'])
                ->add('nationalstatus', null, ['label' => 'Status of the Species'])
                ->add('areaSuccess', null, ['label' => 'Establishement'])
                ->add(
                    'Status',
                    ChoiceType::class,
                    [
                        'choices' => [
                            'Validated' => 'Validated',
                            'Non Validated' => 'Non Validated',
                            'Information requested' => 'Information requested'
                        ],
                        'label' => 'Data Status',
                    ]
                )
                ->end()
                ->with('Admin Info', ['class' => 'col-md-6'])
                //->add('areaNote',null, array('label' => 'Note'))
                //->add (
                //    'createdAt',
                //    DatePickerType::class,
                //    ['label' => 'Created At'],
                //    [
                //        'dp_side_by_side' => true,
                //        'dp_use_current' => true,
                //        'dp_collapse' => true,
                //         'dp_view_mode' => 'days',
                //         'dp_min_view_mode' => 'days',
                //     ]
                // )
                ->add(
                    'updatedAt',
                    DatePickerType::class,
                    ['label' => 'Updated At'],
                    [
                        'dp_side_by_side' => true,
                        'dp_use_current' => true,
                        'dp_collapse' => true,
                        'dp_calendar_weeks' => false,
                        'dp_view_mode' => 'days',
                        'dp_min_view_mode' => 'year',
                    ]
                )
                ->end();
        } else {
            $formMapper
                ->with('Species Details', ['class' => 'col-md-6'])
                ->add('mamias', null, ['label' => 'Species Name'])
                ->add('country', null, ['label' => 'Country'])
                ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion'])
                ->add('regionalSea', null, ['label' => 'Regional Seas'])
                ->add('VectorName', null, ['label' => 'Pathways'])
                ->add('Certainty', ChoiceType::class,
                    [
                        'choices' => [
                            'Low' => 'Low',
                            'Medium' => 'Medium',
                            'High' => 'High',
                        ],
                        'label' => 'Certainty', 'placeholder' => 'Choose an option', 'required' => false
                    ])
                ->add('nationalstatus', null, ['label' => 'Status of the Species'])
                ->add('AreaSighting', null, ['label' => 'National Sighting'])
                ->add('fisrtSighting', null, ['label' => '1st Sighting'])
                ->add('areaSuccess', null, ['label' => 'Country Establishement'])
                //->add('vector', null, ['label' => 'Vector/Pathways'])
                ->add(
                    'Status',
                    ChoiceType::class,
                    [
                        'choices' => [
                            'Validated' => 'Validated',
                            'Non Validated' => 'Non Validated',
                            'Information requested' => 'Information requested'
                        ],
                        'label' => 'Data Status',
                    ]
                )
                ->end()
                ->with('Admin Info', ['class' => 'col-md-6'])
                //->add('areaNote',null, array('label' => 'Note'))
                ->add(
                    'createdAt',
                    DatePickerType::class,
                    ['label' => 'Created At'],
                    [
                        'dp_side_by_side' => true,
                        'dp_use_current' => true,
                        'dp_collapse' => true,
                        'dp_view_mode' => 'days',
                        'dp_min_view_mode' => 'days',
                    ]
                )
                ->add(
                    'updatedAt',
                    DatePickerType::class,
                    ['label' => 'Updated At'],
                    [
                        'dp_side_by_side' => true,
                        'dp_use_current' => true,
                        'dp_collapse' => true,
                        'dp_calendar_weeks' => false,
                        'dp_view_mode' => 'days',
                        'dp_min_view_mode' => 'year',
                    ]
                )
                ->end();
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('country', null, ['label' => 'Country'])
            ->add('ecap', null, ['label' => 'EcAp/MSFD Subregion'])
            ->add('regionalSea', null, ['label' => 'Regional Seas'])
            ->add('AreaSighting', null, ['label' => 'National Sighting'])
            ->add('nationalstatus', null, ['label' => 'Status of the Species'])
            ->add('areaSuccess', null, ['label' => 'Establishement'])
            ->add('VectorName', null, ['label' => 'Vector/Pathways'])
            ->add('areaNote', null, ['label' => 'Note'])
            ->add('createdAt', null, ['label' => 'Created At'])
            ->add('updatedAt', null, ['label' => 'updated At']);
    }
}
