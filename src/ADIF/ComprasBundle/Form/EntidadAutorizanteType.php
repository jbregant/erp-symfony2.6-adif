<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntidadAutorizanteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacionEntidadAutorizante', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('montoDesde', null, array(
                    'required' => true,
                    'label' => 'Monto desde',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('montoHasta', null, array(
                    'required' => true,
                    'label' => 'Monto hasta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('grupo', 'entity', array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Grupo',
                    'required' => true,
                    'label' => 'Grupo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  choice '))
                )

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\EntidadAutorizante'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_entidadautorizante';
    }

}
