<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PolizaSeguroContratoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numeroPoliza', null, array(
                    'required' => true,
                    'label' => 'N&ordm; p&oacute;liza',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('fechaInicio', 'date', array(
                    'required' => true,
                    'label' => 'Fecha inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('aseguradora', null, array(
                    'required' => true,
                    'label' => 'Aseguradora',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('riesgoCubierto', null, array(
                    'required' => true,
                    'label' => 'Riesgo cubierto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('importe', null, array(
                    'required' => true,
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )
                ->add('numeroTramiteEnvio', null, array(
                    'required' => false,
                    'label' => 'N&ordm; tr&aacute;mite env&iacute;o',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_polizasegurocontrato';
    }

}
