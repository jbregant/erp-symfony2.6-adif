<?php

namespace ADIF\ContableBundle\Form\Obras;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EstadoTramoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigo', null, array(
                    'required' => true,
                    'label' => 'Código',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '))
                )
                ->add('denominacionEstado', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('descripcionEstado', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('tipoImportancia', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ComprasBundle\Entity\TipoImportancia',
                    'label' => 'Tipo importancia',
                    'em' => $options['entity_manager'],
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  choice '))
                )
                ->add('esEditable', null, array(
                    'required' => false,
                    'label' => 'Es editable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('generaAsientoObraFinalizada', null, array(
                    'required' => false,
                    'label' => 'Genera asiento de obra finalizada',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\EstadoTramo'
        ));
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_estadotramo';
    }

}
