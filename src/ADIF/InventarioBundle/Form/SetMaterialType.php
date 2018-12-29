<?php

namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SetMaterialType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('IdComponente', 'entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos',
                    'required' => true,
                    'empty_value' => '-- Material --',
                    'label' => 'Material',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '))
                )

                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\SetMaterial'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_inventariobundle_setmaterial';
    }

}
