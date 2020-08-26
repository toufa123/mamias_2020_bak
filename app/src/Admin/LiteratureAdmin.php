<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class LiteratureAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            //->add('id')
            ->add('mamias')
            ->add('country')
            //->add('DOI')
            //->add('literature')
            //->add('litlink')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('country', null, ['label' => 'Country'])
            ->add('literatureCode', null, ['label' => 'code'])
            ->add('Authors', null, ['label' => 'Short Reference', 'class' => 'col-9'])
            //->add('DOI', null, ['label' => 'DOI'])
            ->add(
                'Title',
                null,
                ['label' => 'Full Reference', 'template' => 'admin/Literature/ref.html.twig']
            )
            //->add('litlink',null, array('label' => 'Link'))
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
            ->add('literatureCode', null, ['label' => 'Species Name'])
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('country', null, ['label' => 'Country'])
            ->add('Authors', null, ['label' => 'Short Reference'])
            ->add('Year', null, ['label' => 'Year'])
            ->add('Title', null, ['label' => 'Full Reference'])
            ->add('DOI', null, ['label' => 'DOI'])
            ->add('where', null, ['label' => 'Journal/Publisher/website'])
            ->add('litlink', null, ['label' => 'Link'])
            ->add('lit_note', null, ['label' => 'Notes'])
            ->add('users');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('literatureCode', null, ['label' => 'Species Name'])
            ->add('mamias', null, ['label' => 'Species Name'])
            ->add('country', null, ['label' => 'Country'])
            ->add('Authors', null, ['label' => 'Short Reference'])
            ->add('Year', null, ['label' => 'Year'])
            ->add('Title', null, ['label' => 'Title'])
            ->add('DOI', null, ['label' => 'DOI'])
            ->add('literature', null, ['label' => 'Full Reference'])
            ->add('where', null, ['label' => 'Journal/Publisher/website'])
            ->add('litlink', null, ['label' => 'Link'])
            ->add('lit_note', null, ['label' => 'Notes'])
            ->add('users');
    }
}
