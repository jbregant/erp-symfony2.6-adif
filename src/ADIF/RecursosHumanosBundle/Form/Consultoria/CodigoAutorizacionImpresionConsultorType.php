<?php

namespace ADIF\RecursosHumanosBundle\Form\Consultoria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CodigoAutorizacionImpresionConsultorType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&ordm; CAI',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control cai-numero'))
                )
                ->add('puntoVenta', null, array(
                    'required' => true,
                    'label' => 'Punto de venta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control cai-punto-venta'))
                )
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Consultoria\CodigoAutorizacionImpresionConsultor'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_consultoria_codigoautorizacionimpresionconsultor';
    }

}
