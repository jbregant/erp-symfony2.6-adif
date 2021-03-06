<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClaseContratoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('codigo', null, array(
                    'required' => true,
                    'label' => 'Codigo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control number'),
                    )
                )
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),
                    )
                )
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripcion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),
                    )
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'label' => 'Cuenta cr&eacute;dito',
                    'attr' => array('class' => 'form-control choice'),
                    'em' => $options['entity_manager']
                    )
                )
                ->add('cuentaIngreso', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => 'form-control choice'),
                    'em' => $options['entity_manager']
                    )
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\ClaseContrato'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_clasecontrato';
    }

}
