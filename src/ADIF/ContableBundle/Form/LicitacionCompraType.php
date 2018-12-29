<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicitacionCompraType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control integerPositive'))
                )
                ->add('anio', 'text', array(
                    'required' => true,
                    'label' => 'A&ntilde;o',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control nomask novalidate'),
                ))
                ->add('tipoContratacion', 'entity', array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoContratacion',
                    'required' => true,
                    'property' => 'aliasYMonto',
                    'label' => 'Tipo contrataci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice')
                ))
                ->add('fechaApertura', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha apertura',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('importePliego', null, array(
                    'required' => false,
                    'label' => 'Importe pliego',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )
                ->add('importeLicitacion', null, array(
                    'required' => false,
                    'label' => 'Importe licitaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\LicitacionCompra'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_licitacioncompra';
    }

}
