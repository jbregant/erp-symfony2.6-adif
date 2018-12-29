<?php

namespace ADIF\ComprasBundle\Form;

use ADIF\ComprasBundle\Form\AdjuntoExencionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CertificadoExencionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numeroCertificado', null, array(
                    'required' => true,
                    'label' => 'Número',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('tipoRegimen', null, array(
                    'required' => true,
                    'label' => 'Régimen',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('fechaDesde', 'date', array(
                    'required' => true,
                    'label' => 'Fecha desde',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker'), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaHasta', 'date', array(
                    'required' => true,
                    'label' => 'Fecha hasta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker'), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('porcentajeExencion', null, array(
                    'required' => true,
                    'label' => 'Porcentaje exención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('adjunto', new AdjuntoExencionType(), array(
                    'required' => false)
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\CertificadoExencion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_certificadoexencion';
    }

}
