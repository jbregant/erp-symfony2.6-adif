<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DetalleConceptoFormulario572Type extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cuit', null, array(
                    'label' => 'CUIT',
                    'attr' => array('class' => ' form-control cuitConcepto inputEditable'),))
                ->add('detalle', null, array(
                    'label' => 'Detalle',
                    'attr' => array('class' => ' form-control inputEditable'),))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\DetalleConceptoFormulario572',
            'cascade_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_detalle_concepto_formulario_572';
    }

}
