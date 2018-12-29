<?php

namespace ADIF\ContableBundle\Form\ConciliacionBancaria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConceptoConciliacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  '),))
                ->add('esContabilizable', null, array(
                    'required' => false,
                    'label' => 'Es contabilizable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('cuentaContable', EntityType::clase, array(                    
                    'required' => false,                   
                    'empty_value' => '-- Cuenta contable --',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager']

        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion'
        ));
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_conciliacionbancaria_conceptoconciliacion';
    }

}
