<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrioridadType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacionPrioridad', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cantidadDias', 'number', array(
                    'required' => true,
                    'label' => 'Cantidad de días',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\Prioridad'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_prioridad';
    }

}
