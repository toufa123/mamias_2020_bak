<?php

declare(strict_types=1);

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DateTimePickerType;

final class PageAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            //->add('id')
            ->add('title')
            ->add('slug')
            //->add('content')
            //->add('metaDescription')
            //->add('metaTitle')
            //->add('metaKeywords')
            //->add('css')
            //->add('js')
            ->add('createdAt')
            ->add('enabled')
            ->add('homepage')
            //->add('host')
            ->add('locale');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            //->add('id')
            ->addIdentifier('title')
            ->add('slug')
            //->add('content')
            //->add('metaDescription')
            //->add('metaTitle')
            //->add('metaKeywords')
            ->add('parent', null)
            ->add('category', null)
            //->add('css')
            //->add('js')
            ->add('createdAt')
            //->add('enabled')
            ->add(
                'enabled',
                'boolean',
                [
                    'editable' => true,
                    'choices' => [
                        1 => 'Yes',
                        2 => 'No',
                    ],
                ]
            )
            ->add(
                'homepage',
                'boolean',
                [
                    'editable' => true,
                    'choices' => [
                        1 => 'Yes',
                        2 => 'No',
                    ],
                ]
            )
            //->add('host')
            ->add('locale')
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
            //->add('id')
            ->add('title', null, ['label' => 'Title'])
            ->add('slug', null, ['label' => 'Slug'])
            ->add('parent', null)
            ->add('category', null)
            ->add('metaDescription')
            ->add('metaTitle')
            ->add('metaKeywords')
            //->add('css')
            //->add('js')
            ->add(
                'content',
                CKEditorType::class,
                [
                    'config' => array(
                        'filebrowserBrowseRoute' => 'elfinder',
                        'filebrowserBrowseRouteParameters' => array(
                            'instance' => 'default',
                            'homeFolder' => ''
                        )
                    )
                ],

                ['label' => 'Content']
            )
            ->add('createdAt', DateTimePickerType::class, ['label' => 'Created At'])
            ->add('enabled')
            ->add('homepage')
            ->add('host')
            ->add('locale');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('metaDescription')
            ->add('metaTitle')
            ->add('metaKeywords')
            ->add('parent', null)
            ->add('category', null)
            ->add('content')

            //->add('css')
            //->add('js')
            ->add('createdAt')
            ->add('enabled')
            ->add('homepage')
            ->add('host')
            ->add('locale');
    }
}
