<?php

namespace ADIF\ContableBundle\Form\ Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlicuotaIngresosBrutosVentaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigo', null, array(
                    'required' => true,
                    'label' => 'C&oacute;digo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ', 'readonly' => true)))
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Descripci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ', 'readonly' => true)))
                ->add('valor', null, array(
                    'required' => true,
                    'label' => 'Valor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency ')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\AlicuotaIngresosBrutosVenta'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_alicuotaingresosbrutosventa';
    }

}
