<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class GerenciaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('centroCosto', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CentroCosto',
                    'label' => 'Centro de costo',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_conta'],
                    'attr' => array('class' => 'form-control choice'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Gerencia'
        ));
        
        $resolver->setRequired('entity_manager_conta');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_gerencia';
    }

}
