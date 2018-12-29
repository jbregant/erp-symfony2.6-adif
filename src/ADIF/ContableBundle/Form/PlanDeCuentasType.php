<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


/**
 * PlanDeCuentasType
 * 
 * @author Manuel Becerra
 * created 24/06/2014
 * 
 */
class PlanDeCuentasType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('segmentos', 'collection', array(
                    'type' => new SegmentoPlanDeCuentasType($options['entity_manager']),
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Segmentos',
                    'prototype_name' => '__segmento__')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\PlanDeCuentas'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_plandecuentas';
    }

}
