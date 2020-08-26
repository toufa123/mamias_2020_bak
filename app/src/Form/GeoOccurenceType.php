<?php

namespace App\Form;

use App\Entity\GeoOccurence;
use App\Entity\Mamias;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use CrEOF\Spatial\DBAL\Types\Geometry\PointType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\Extension\Core\Type\ResetType;


class GeoOccurenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', null, ['label' => 'Country', 'required' => true
                //,'help' => 'Choose a country'
            ])
            ->add(
                'mamias',
                EntityType::class,
                [
                    'class' => Mamias::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.relation', 'ASC');
                    },
                    'choice_label' => 'relation',
                    'label' => 'Species Name',
                    'required' => true, 'empty_data' => ''//, 'help' => 'Choose a Species'
                ]
            )
            ->add('Location', null, ['label' => 'Coordinates', 'required' => true, 'empty_data' => '',
                'help' => ' Move marker to get location'])
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label' => 'Photo',
                    'required' => true,
                    'allow_delete' => true,
                    //'download_label' => 'azerty',
                    'download_uri' => false,
                    'image_uri' => false,
                    'attr' => [
                        //'type' => 'file',
                        //'class' => 'filestyle',
                        //'placeholder' => 'Upload the taken photo',
                        'data-buttontext' => 'Select a photo',
                        'data-buttonname' => 'btn-custom',
                        'data-size' => 'sm',
                        'data-preview-file-type' => 'text',
                        'multiple' => '',
                        'data-allowed-file-extensions' => '["jpeg", "png", "jpg"]',

                    ],
                ]
            )
            //->add('latitude', null, ['label' => 'Latitude', 'required' => true])
            //->add('longitude', null, ['label' => 'Longitude', 'required' => true,])
            ->add('date_occurence', DateType::class, ['widget' => 'single_text', 'html5' => false,
                'format' => 'dd-mm-yyyy', 'help' => 'Reporting date',
                'label' => 'Reporting date',
                'required' => true,
            ])
            ->add('depth', IntegerType::class, ['label' => 'Depth', 'required' => false,
                'help' => 'depth in meter'])
            ->add('PlantsAnimals', ChoiceType::class, ['choices' => ['' => '', 'Coverage in m2' => 'Coverage in m2',
                'Numbre of indivudals' => 'Numbre of indivudals'],
                'label' => 'Type of observation',
                'required' => false, 'help' => 'type of observation'])
            ->add('nvalues', IntegerType::class, ['label' => 'Values', 'required' => false,
                    'help' => 'nb of indivudals or Coverage in m2']
            )
            ->add('EstimatedMeasured', ChoiceType::class, ['choices' => ['Estimated' => 'Estimated',
                'Measured' => 'Measured'],
                //'expanded' => true, 'multiple' => false,
                //'label_attr' => [
                //'class' => 'radio-custom'
                //], 'label' => 'Accuracy',
                'required' => false, 'help' => 'Accurancy',
            ])
            ->add('note_occurence', TextareaType::class, ['label' => 'Notes',
                'required' => false, 'empty_data' => ''])
            ->add('recaptcha', EWZRecaptchaType::class, array(
                'attr' => array(
                    'options' => array(
                        'theme' => 'light',
                        'type' => 'image',
                        'size' => 'normal'
                    )
                ),
                'mapped' => false,
                'constraints' => array(
                    new RecaptchaTrue()
                )
            ));
        //->add('createdAt', DateType::class, ['label' => 'Date of reporting', 'widget' => 'single_text', 'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => GeoOccurence::class,
            ]
        );
    }
}
