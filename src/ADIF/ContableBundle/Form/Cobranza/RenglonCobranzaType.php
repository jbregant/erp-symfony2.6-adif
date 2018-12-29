<?php

namespace ADIF\ContableBundle\Form\ Cobranza;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RenglonCobranzaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fecha', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('numeroTransaccion', null, array(
                    'required' => true,
                    'label' => 'Referencia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number'),));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cobranza_rengloncobranza';
    }

}
