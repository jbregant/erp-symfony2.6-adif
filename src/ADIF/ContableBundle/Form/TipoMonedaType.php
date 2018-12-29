<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipoMonedaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigoTipoMoneda', null, array(
                    'required' => true,
                    'label' => 'Código',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('simboloTipoMoneda', null, array(
                    'required' => true,
                    'label' => 'Símbolo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('denominacionTipoMoneda', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                /* Es MCL */
                ->add('esMCL', null, array(
                    'required' => false,
                    'label' => 'Es moneda curso legal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('tipoCambio', null, array(
                    'required' => true,
                    'label' => 'Tipo de cambio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '4'
                    ))
                )
                ->add('descripcionTipoMoneda', null, array(
                    'required' => false,
                    'label' => 'Descripción',
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
            'data_class' => 'ADIF\ContableBundle\Entity\TipoMoneda'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_tipomoneda';
    }

}
