<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class PresupuestoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('ejercicioContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EjercicioContable',
                    'empty_value' => '-- Ejercicio contable --',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array(
                        'class' => ' form-control choice ',
                    ),
                ))
                ->add('cuentasPresupuestarias', 'collection', array(
                    'type' => new CuentaPresupuestariaType($options['entity_manager_contable']),
                    'allow_add' => true)
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Presupuesto'
        ));
        $resolver->setRequired('entity_manager_contable');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_presupuesto';
    }

}
