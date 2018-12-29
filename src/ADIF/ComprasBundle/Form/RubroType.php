<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RubroType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacionRubro', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('descripcionRubro', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('area', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Area',
                    'required' => true,
                    'label' => 'Área',
                    'empty_value' => '-- Área --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    //'em' => $options['entity_manager_RRHH'],
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\Rubro'
        ));
        
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_RRHH');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_rubro';
    }

}
