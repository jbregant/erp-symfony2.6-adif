<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EgresoValorType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tipoEgresoValor', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor',
                    'label' => 'Tipo',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('carpeta', null, array(
                    'required' => true,
                    'label' => 'Carpeta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('responsableEgresoValor', new ResponsableEgresoValorType($options['entity_manager_rrhh']), array(
                    'required' => true)
                )
                ->add('gerencia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Gerencia',
                    'required' => true,
                    'label' => 'Gerencia',
                    'em' => $options['entity_manager_rrhh'],
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('importe', null, array(
                    'required' => true,
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
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
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\EgresoValor'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_egresovalor_egresovalor';
    }

}
