<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class AreaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'attr' => array('class' => ' form-control '),)
                )
                ->add('centroCosto', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CentroCosto',
                    'label' => 'Centro de costos',
                    'required' => true,
                    'multiple' => false,
                    'em' => $options['entity_manager'],                
                    'attr' => array('class' => ' form-control choice ', 
                                    'placeholder' => '-- Seleccione el centro de costos --'),
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Area'
        ));
        
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_RRHH');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_area';
    }

}
