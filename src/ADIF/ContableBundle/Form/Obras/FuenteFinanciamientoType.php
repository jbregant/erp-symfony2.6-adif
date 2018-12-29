<?php

namespace ADIF\ContableBundle\Form\Obras;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class FuenteFinanciamientoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigo', null, array(
                    'required' => true,
                    'label' => 'Código',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '))
                )
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'required' => false,
                    'label' => 'Cuenta contable',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('modificaCuentaContable', null, array(
                    'required' => false,
                    'label' => 'Modifica cuenta contable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_fuentefinanciamiento';
    }

}
