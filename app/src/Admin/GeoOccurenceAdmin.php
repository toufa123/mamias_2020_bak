<?php

declare(strict_types=1);

namespace App\Admin;

use CrEOF\Geo\WKT\Parser;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

final class GeoOccurenceAdmin extends AbstractAdmin
{


    protected $baseRouteName = 'Geo';
    protected $classnameLabel = 'GeoOccurence';

    public function prePersist($object)
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $object->setUser($user);

        $parser = new Parser('Point(' . $object->getLocation() . ')');
        $geo = $parser->parse();
        $g = new \CrEOF\Spatial\PHP\Types\Geometry\Point($geo['value'], '4326');
        $object->setLocation($g);
        //dump ('prepersist',$geo,$g);die;


    }

    public function preUpdate($object)
    {
        $parser = new Parser('Point(' . $object->getLocation() . ')');
        $geo = $parser->parse();
        $g = new \CrEOF\Spatial\PHP\Types\Geometry\Point($geo['value'], '4326');
        $object->setLocation($g);
        //dump ('preupdate',$geo,$g);die;
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $object->setUser($user);
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            //->add('id')

            ->add('mamias', null, ['label' => 'Species Name', 'show_filter' => true])
            ->add('date_occurence', null, ['label' => 'Date of the Occurence', 'format' => 'd/M/y'])
            ->add('createdAt', null, ['label' => 'Created At', 'format' => 'y'])
            //->add('updatedAt', null, array('label' => 'Updated At', 'format' => 'dd/MM/y'))
            //->add('validator', null, ['label' => 'Validated by'])
            ->add(
                'status',
                null,
                ['label' => 'Status', 'show_filter' => true],
                ChoiceType::class,
                [
                    'choices' => [
                        'Validated' => 'Validated',
                        'Submitted' => 'Submitted',
                        'Rejected' => 'Rejected'
                        //'Information requested' => 'Information requested'
                    ],
                    'label' => 'Status',
                ]
            )
            ->add('user', null, ['label' => 'declared By', 'show_filter' => true])
            ->add('country', null, ['label' => 'Country', 'show_filter' => true]);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('mamias', null, ['label' => 'Species Name', 'header_style' => 'width: 10%'])
            ->add('country', null, ['label' => 'Country'])
            ->add('imageFile', null, ['label' => 'Picture', 'template' => 'declaration/picture.html.twig'])
            ->add('Location', null, ['label' => 'Coordinates'])
            //->add('longitude', null, array('label' => 'longitude'))
            ->add('date_occurence', DateTimeType::class, ['label' => 'Reporting Date', 'template' => 'admin/Mamias/date.html.twig'])
            //->add('note_occurence', null, array('label' => 'Note'))
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => [
                        'choices' => [
                            'Validated' => 'Validated',
                            'Rejected' => 'Rejected',
                            'Submitted' => 'Submitted'
                        ],
                        'editable' => true
                    ],
                    ['label' => 'Status'],
                ]
            )
            //->add('createdAt', null, ['label' => 'Created At', 'format' => 'd/M/y'])
            //->add('updatedAt', null, array('label' => 'Updated At','format' => 'd/M/y'))
            //->add('user', null, ['label' => 'declared By'])
            ->add('validator', null, ['label' => 'Validated by', 'disabled' => true])
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
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $formtest1 = $this->id($this->getSubject());
        //$formtest2 = $this->getSubject ()->getId ();
        //dump($user);die;

        if (null === $formtest1) {
            if ($this->hasParentFieldDescription()) {
                //to be added through MAMIAS
                $formMapper
                    ->add('country', null, ['label' => 'Country', 'disabled' => false])
                    ->add('Location', null, ['label' => 'Coordinates', 'disabled' => false, 'required' => true])
                    ->add('date_occurence', DatePickerType::class,
                        ['label' => 'Reporting Date', 'disabled' => false],
                        ['format' => 'dd/mm/yyyy', 'dp_use_current' => true, 'dp_show_today' => true, 'dp_view_timezone' => 'Europe/Paris', 'dp_model_timezone' => 'Europe/Paris',
                            'dp_collapse' => true, 'dp_view_mode' => 'decades', 'dp_min_view_mode' => 'years'])
                    ->add(
                        'imageFile',
                        VichImageType::class, [
                            'label' => 'Picture',
                            'required' => false,
                            //'download_link' => false,
                            'allow_delete' => false,
                            //'download_uri' => false,
                            'image_uri' => true,
                            'disabled' => false

                        ]
                    )
                    ->add('depth', IntegerType::class, ['label' => 'Depth', 'required' => false,
                    ])
                    ->add('PlantsAnimals', ChoiceType::class, ['choices' => ['' => '', 'Coverage in m2' => 'Coverage in m2',
                        'Numbre of indivudals' => 'Numbre of indivudals'],
                        'label' => 'Type of observation',
                        'required' => false,])
                    ->add('nvalues', IntegerType::class, ['label' => 'Values', 'required' => false,
                        ]
                    )
                    ->add('EstimatedMeasured', ChoiceType::class, ['choices' => ['Estimated' => 'Estimated',
                        'Measured' => 'Measured'], 'expanded' => false, 'multiple' => false,
                        'label_attr' => [
                            'class' => 'radio-inline radio-primary'
                        ], 'label' => 'Accuracy',
                        'required' => false,
                    ])




                    //->add('user', null, ['label' => 'declared By', 'disabled' => false, 'data' => $user])
                    ->add(
                        'status',
                        ChoiceType::class,
                        [
                            'choices' => [
                                'Validated' => 'Validated',
                                'Submitted' => 'Submitted',
                                'Rejected' => 'Rejected'
                                //'Information requested' => 'Information requested'
                            ],
                            'label' => 'Status',
                        ]
                    )
                    ->add('validator', EntityType::class, ['label' => 'Validated by', 'disabled' => false, 'required' => false,
                        'class' => User::class,
                        'expanded' => false,
                        'multiple' => true,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('d')
                                ->where('d.roles  LIKE :ROLE')
                                ->setParameter('ROLE', '%"' . 'ROLE_S' . '"%');
                        }])
                    ->add('notes', null, ['label' => 'Comments by the Admin']);
            } else {
                //added through Geo
                $formMapper
                    ->add('mamias', null, ['label' => 'Species Name', 'disabled' => false])
                    ->add('country', null, ['label' => 'Country', 'disabled' => false])
                    ->add('Location', null, ['label' => 'Coordinates', 'disabled' => false, 'required' => true])
                    ->add(
                        'date_occurence',
                        DatePickerType::class,
                        ['label' => 'Reporting Date', 'disabled' => false],
                        [
                            'format' => 'dd/mm/yyyy',
                            'dp_use_current' => true,
                            'dp_show_today' => true,
                            'dp_collapse' => true,
                            'dp_view_mode' => 'years',
                            'dp_min_view_mode' => 'years',
                            'dp_view_timezone' => 'Europe/Paris', 'dp_model_timezone' => 'Europe/Paris',
                        ]
                    )
                    ->add(
                        'imageFile',
                        VichImageType::class, [
                            'label' => 'Picture',
                            'required' => false,
                            //'download_link' => false,
                            'allow_delete' => false,
                            //'download_uri' => false,
                            'image_uri' => true,
                            'disabled' => false

                        ]
                    )
                    ->add('depth', IntegerType::class, ['label' => 'Depth', 'required' => false,
                    ])
                    ->add('PlantsAnimals', ChoiceType::class, ['choices' => ['' => '', 'Coverage in m2' => 'Coverage in m2',
                        'Numbre of indivudals' => 'Numbre of indivudals'],
                        'label' => 'Type of observation',
                        'required' => false,])
                    ->add('nvalues', IntegerType::class, ['label' => 'Values', 'required' => false,
                        ]
                    )
                    ->add('EstimatedMeasured', ChoiceType::class, ['choices' => ['Estimated' => 'Estimated',
                        'Measured' => 'Measured'], 'expanded' => false, 'multiple' => false,
                        'label_attr' => [
                            'class' => 'radio-inline radio-primary'
                        ], 'label' => 'Accuracy',
                        'required' => false,
                    ])


                    //->add('user', null, ['label' => 'declared By', 'disabled' => true, 'data' => $user])
                    ->add(
                        'status',
                        ChoiceType::class,
                        [
                            'choices' => [
                                'Validated' => 'Validated',
                                'Submitted' => 'Submitted',
                                'Rejected' => 'Rejected'
                                //'Information requested' => 'Information requested'
                            ],
                            'label' => 'Status',
                        ]
                    )
                    //->add ('validator', null, ['label' => 'Validated by', 'disabled' => false])
                    ->add('validator', EntityType::class, ['label' => 'Validated by', 'disabled' => false, 'required' => false,
                        'class' => User::class,
                        'expanded' => false,
                        'multiple' => true,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('d')
                                ->where('d.roles  LIKE :ROLE')
                                ->setParameter('ROLE', '%"' . 'ROLE_S' . '"%');
                        }])
                    ->add('notes', null, ['label' => 'Comments by the Admin']);
            }
        } else {
            // mamias already added
            $formMapper
                ->add('country', null, ['label' => 'Country', 'disabled' => true])
                ->add('Location', null, ['label' => 'Coordinates', 'disabled' => true, 'required' => true])
                ->add(
                    'date_occurence',
                    DatePickerType::class,
                    ['label' => 'Reporting Date', 'disabled' => true],
                    [
                        'format' => 'dd/mm/yyyy',
                        'dp_use_current' => true,
                        'dp_show_today' => true,
                        'dp_collapse' => true,
                        'dp_view_mode' => 'years',
                        'dp_min_view_mode' => 'years',
                        'dp_view_timezone' => 'Europe/Paris', 'dp_model_timezone' => 'Europe/Paris',]
                )
                ->add(
                    'imageFile',
                    VichImageType::class, [
                        'label' => 'Picture',
                        'required' => false,
                        //'download_link' => false,
                        'allow_delete' => false,
                        //'download_uri' => false,
                        'image_uri' => true,
                        'disabled' => false

                    ]
                )
                ->add('depth', IntegerType::class, ['label' => 'Depth', 'required' => false,
                ])
                ->add('PlantsAnimals', ChoiceType::class, ['choices' => ['' => '', 'Coverage in m2' => 'Coverage in m2',
                    'Numbre of indivudals' => 'Numbre of indivudals'],
                    'label' => 'Type of observation',
                    'required' => false,])
                ->add('nvalues', IntegerType::class, ['label' => 'Values', 'required' => false,
                    ]
                )
                ->add('EstimatedMeasured', ChoiceType::class, ['choices' => ['Estimated' => 'Estimated',
                    'Measured' => 'Measured'], 'expanded' => false, 'multiple' => false,
                    'label_attr' => [
                        'class' => 'radio-inline radio-primary'
                    ], 'label' => 'Accuracy',
                    'required' => false,
                ])



                //->add('user', null, ['label' => 'declared By', 'disabled' => true, 'data' => $user])
                ->add(
                    'status',
                    ChoiceType::class,
                    [
                        'choices' => [
                            'Validated' => 'Validated',
                            'Submitted' => 'Submitted',
                            'Rejected' => 'Rejected'
                            //'Information requested' => 'Information requested'
                        ],
                        'label' => 'Status',
                    ]
                )
                ->add('validator', EntityType::class, ['label' => 'Validated by', 'disabled' => false, 'required' => false,
                    'class' => User::class,
                    'expanded' => false,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->where('d.roles  LIKE :ROLE')
                            ->setParameter('ROLE', '%"' . 'ROLE_S' . '"%');
                    }])
                ->add('notes', null, ['label' => 'Comments by the Admin']);
        }
    }


    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('mamias', null, ['label' => 'Species Name'])
            //->add('imageFile', 'vich_image')
            ->add('country', null, ['label' => 'Country'])
            ->add('Location', PointType::class, ['label' => 'Location'])
            //->add('longitude', null, array('label' => 'longitude'))
            ->add('date_occurence', null, ['label' => 'Date of the Occurence'])
            ->add('imageFile', null, ['label' => 'Picture', 'template' => 'declaration/picture.html.twig'])
            ->add('note_occurence', null, ['label' => 'Note'])
            ->add('createdAt', null, ['label' => 'Created At'])
            ->add('updatedAt', null, ['label' => 'Updated At'])
            ->add('status', null, ['label' => 'Status'])
            ->add('user', null, ['label' => 'declared By']);
    }
}
