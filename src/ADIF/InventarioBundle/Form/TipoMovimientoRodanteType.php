<?php

namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipoMovimientoRodanteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ') ))
                ->add('funcion', 'choice', array(
                    'required'  => true,
                    'label' => 'Funci&oacute;n', 
                    'label_attr' => array('class' => 'control-label'),
                    'choices'   => array('I' => 'Ingreso', 'E' => 'Egreso'),
                    'attr' => array('class' => ' form-control ') ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\TipoMovimientoRodante'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_inventariobundle_tipomovimientorodante';
    }

}
