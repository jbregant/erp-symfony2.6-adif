<?php

namespace ADIF\ContableBundle\Form\Cobranza;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonCobranzaChequeType extends AbstractType {

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
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('banco', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Banco',                    
                    'empty_value' => '-- Banco --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Banco',
                    'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),))                
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number'),))
                ->add('observacion', 'textarea', array(
                    'required' => false,
                    'label' => 'Observaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'rows' => '2')
                        )
                );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque'
        ));
        $resolver->setRequired('entity_manager_conta');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cobranza_rengloncobranzacheque';
    }

}
