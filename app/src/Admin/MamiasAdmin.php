<?php

namespace App\Admin;

use App\Entity\Catalogue;
use App\Entity\GeoOccurence;
use App\Entity\CountryDistribution;
use CrEOF\Geo\WKT\Parser;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class MamiasAdmin extends AbstractAdmin
{
    protected $classnameLabel = 'MAMIAS';

    //public function toString($object)
    //{
    //  return $object instanceof Sport
    //    ? $object->getTitle()
    //  : 'Sport'; // shown in the breadcrumb on the create view
    //}

    protected $perPageOptions = [10, 20, 50, 100, 'All'];
    protected $maxPerPage = '20';
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'Species',
        '_per_page' => '20',
    ];
    protected $baseRouteName = 'mamias';

    public function getExportFormats()
    {
        return ['xls', 'xml', 'json'];
    }


    public function prePersist($object)
    {
        foreach ($object->getDistribution() as $Distribution) {
            $Distribution->setMamias($object);
        }

        $GeoAdmin = $this
            ->getConfigurationPool()
            ->getAdminByAdminCode('admin.geo_occurence');

        foreach ($object->getGeo() as $Geo) {
            $Geo->setMamias($object);
            $GeoAdmin->prePersist($Geo);

        }

        foreach ($object->getLit() as $Lit) {
            $Lit->setMamias($object);
        }

        foreach ($object->getPathway() as $path) {
            $path->setMamias($object);
        }
    }

    public function preUpdate($object)
    {
        foreach ($object->getDistribution() as $Distribution) {
            $Distribution->setMamias($object);
        }
        $GeoAdmin = $this
            ->getConfigurationPool()
            ->getAdminByAdminCode('admin.geo_occurence');
        foreach ($object->getGeo() as $Geo) {
            $Geo->setMamias($object);
            $GeoAdmin->prePersist($Geo);
        }
        foreach ($object->getLit() as $Lit) {
            $Lit->setMamias($object);
        }
        foreach ($object->getPathway() as $path) {
            $path->setMamias($object);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('relation', null, ['label' => 'Species Name', 'show_filter' => true])
            ->add('Ecofunctional', null, ['label' => 'Ecofunctional Group'])
            //need to get data
            ->add('firstMedSighting', null, ['label' => '1st Med Sighting'])
            ->add('Origin', null, ['label' => 'Origin'])
            ->add('speciesstatus', null, ['label' => 'Status of the species'])
            ->add('Success', null, ['label' => 'Establishement'])
            ->add('vectors', null, ['label' => 'Pathway/Vectors'])
            ->add('Pathway', null, ['label' => 'Pathway-CBD'])
            ->add('createdAt', 'doctrine_orm_datetime', ['field_type' => DatePickerType::class, 'label' => 'Created At'])
            ->add('updatedAt', 'doctrine_orm_datetime', ['field_type' => DatePickerType::class, 'label' => 'Updated At']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', IntegerType::class, ['label' => 'ID', 'header_style' => 'width: 1%'])
            ->add('relation', null, ['label' => 'Species Name', 'header_style' => 'width: 20%'])
            ->add('Ecofunctional', null, ['label' => 'Ecofunctional Group', 'header_style' => 'width: 10%'])
            ->add('firstMedSighting', null, ['label' => '1st Med Sighting', 'header_style' => 'width: 5%;text-align: center', 'row_align' => 'center'])
            ->add('FMedCitation', null, ['label' => '1st Med Citation', 'collapse' => true])
            ->add('Origin', null, ['label' => 'Origin'])
            ->add('speciesstatus', null, ['label' => 'Status of the species', 'header_style' => 'width: 5%;text-align: center'])
            ->add('Success', null, ['label' => 'Establishement', 'header_style' => 'width: 5%'])
            //->add('VectorName',null, array('label' => 'Pathway/Vector'))
            //->add('createdAt', null, array('label' => 'Created At'))
            //->add('updatedAt', null, array('label' => 'Updated At'))
            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        //'show' => [],
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('Non-Indigenous Species')
            ->with('Species Details', ['class' => 'col-md-6'])->end()
            ->with('Introduction of the Species', ['class' => 'col-md-6'])->end()
            ->with('Pathways', ['class' => 'col-md-8'])->end()
            ->with('Admin', ['class' => 'col-md-4'])->end()
            ->end()
            ->tab('Country-level occurrences')
            ->end()
            ->tab('Geo-referenced records')
            ->end();

        $subject = $this->getSubject();
        //dump($subject);die;

        if (null === $this->getSubject()->getId()) {
            $formMapper
                ->tab('Non-Indigenous Species')
                ->with('Species Details')
                ->add('relation', EntityType::class,
                    ['placeholder' => 'Choose a Species',
                        'class' => Catalogue::class,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->orderBy('u.Species', 'ASC');
                        }, 'choice_label' => 'Species', 'label' => 'Species Name',])
                ->add('Ecofunctional', null, ['label' => 'Ecofunctional Group'])
                ->add('invasive', null, ['label' => 'Impact'])
                //->add('modificationDate', DatePickerType::class, ['label' => 'Date of modification'])
                ->add('notes', TextareaType::class, ['label' => 'Note', 'required' => false])
                ->end()
                ->with('Introduction of the Species')
                ->add('firstMedSighting', Null, ['label' => '1st Med Sighting', 'required' => false])
                ->add('FMedCitation', null, ['label' => '1st Med Citation', 'required' => false])
                ->add('Origin', null, ['label' => 'Origin', 'required' => false])
                ->add('speciesstatus', null, ['label' => 'Status of the species', 'required' => false])
                ->add('Success', null, ['label' => 'Establishement'])
                ->end()
                ->with('Pathways')
                ->add('vectors', null, ['label' => 'Pathway/Vectors'])
                ->add('Pathway', CollectionType::class,
                    ['label' => 'Pathway CBD 2014', 'type_options' => ['delete' => true],
                    ], ['by_reference' => true, 'edit' => 'inline', 'inline' => 'table', 'required' => true])
                ->end()
                ->with('Admin')
                ->add('createdAt', DatePickerType::class, ['label' => 'Created At'],
                    ['formate' => 'd/m/y', 'dp_side_by_side' => true, 'dp_use_current' => true,
                        'dp_use_seconds' => true, 'dp_collapse' => true,])
                ->add('updatedAt', DatePickerType::class, ['label' => 'Updated At'], ['formate' => 'd/m/y',
                    'dp_side_by_side' => true, 'dp_use_current' => true, 'dp_use_seconds' => true,
                    'dp_collapse' => true,])
                ->end()
                ->end()
                //->with('scientific literature', ['class' => 'col-md-6'])
                //      ->add('Lit', CollectionType::class, array('label' => 'Literature ',
                //        'type_options' => array('delete' => true)), array('by_reference' => true,'edit' => 'inline','inline' => 'table','required' => true,))
                //->end()

                ->tab('Country-level occurrences')
                ->with('Occurrences')
                ->add('Distribution', CollectionType::class, ['label' => 'Country Level Occurence of ',
                    'type_options' => ['delete' => true]], ['by_reference' => true, 'edit' => 'inline',
                    'inline' => 'table', 'required' => true])
                ->end()
                ->end()
                ->tab('Geo-referenced records')
                ->with('Geo Occurence')
                ->add('Geo', CollectionType::class, ['label' => 'Geo-Occurence of ', 'type_options' => ['delete' => true],
                ], ['by_reference' => true, 'edit' => 'inline', 'inline' => 'table', 'required' => false])
                ->end()
                ->end(); //->add('id')
        } else {
            $formMapper
                ->tab('Non-Indigenous Species')
                ->with('Species Details')
                ->add('relation', TextType::class, ['label' => 'Species Name', 'disabled' => 'true'])
                ->add('Ecofunctional', null, ['label' => 'Ecofunctional Group'])
                ->add('invasive', null, ['label' => 'Impact'])
                //->add('modificationDate', DatePickerType::class, ['label' => 'Date of modification'])
                ->add('notes', TextareaType::class, ['label' => 'Note', 'required' => false])
                ->end()
                ->with('Introduction of the Species')
                ->add('firstMedSighting', Null, ['label' => '1st Med Sighting', 'required' => false])
                ->add('FMedCitation', null, ['label' => '1st Med Citation'])
                ->add('Origin', null, ['label' => 'Origin'])
                ->add('speciesstatus', null, ['label' => 'Status of the species', 'required' => false])
                ->add('Success', null, ['label' => 'Establishement'])
                ->end()
                ->with('Pathways')
                ->add('vectors', null, ['label' => 'Pathway/Vectors'])
                ->add('Pathway', CollectionType::class, ['label' => 'Pathway CBD 2014', 'type_options' => ['delete' => true],
                ], ['by_reference' => true, 'edit' => 'inline', 'inline' => 'table', 'required' => true])
                ->end()
                ->with('Admin')
                ->add('createdAt', DatePickerType::class, ['label' => 'Created At'], ['formate' => 'd/m/y',
                    'dp_side_by_side' => true, 'dp_use_current' => true, 'dp_use_seconds' => true, 'dp_collapse' => true])
                ->add('updatedAt', DatePickerType::class, ['label' => 'Updated At'], ['formate' => 'd/m/y', 'dp_side_by_side' => true,
                    'dp_use_current' => true, 'dp_use_seconds' => true, 'dp_collapse' => true,])
                ->end()
                //->end()
                //->with('scientific literature', ['class' => 'col-md-6'])
                //      ->add('Lit', CollectionType::class, array('label' => 'Literature ',
                //        'type_options' => array('delete' => true)), array('by_reference' => true,'edit' => 'inline','inline' => 'table','required' => true,))
                //->end()
                ->end()
                ->tab('Country-level occurrences')
                ->with('')
                ->add('Distribution', CollectionType::class, ['label' => 'Country Level Occurence', 'type_options' => ['delete' => true],
                ], ['by_reference' => true, 'edit' => 'inline', 'inline' => 'table', 'required' => false])
                ->end()
                //->end()
                ->end()
                ->tab('Geo-referenced records')
                ->with('')
                ->add('Geo', CollectionType::class, ['label' => 'Geo-Occurence ', 'type_options' => ['delete' => true],
                ], ['by_reference' => true, 'edit' => 'inline', 'inline' => 'table', 'required' => false])
                ->end()
                ->end(); //->add('id')
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('relation', null, ['label' => 'Species Name'])
            ->add('Ecofunctional', null, ['label' => 'Ecofunctional Group'])
            ->add('firstMedSighting', null, ['label' => '1st Med Sighting'])
            ->add('FMedCitation', null, ['label' => '1st Med Citation'])
            ->add('Origin', null, ['label' => 'Origin'])
            ->add('speciesstatus', null, ['label' => 'Status of the species', 'required' => false])
            ->add('Success', null, ['label' => 'Establishement'])
            ->add('Pathway', null, ['label' => 'Pathway/Vector'])
            ->add('createdAt', null, ['label' => 'Created At'])
            ->add('updatedAt', null, ['label' => 'Updated At']);
        //->add('Distribution', 'sonata_type_collection');
    }
}
