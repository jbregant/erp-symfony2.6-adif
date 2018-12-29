<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DatoContactoType extends AbstractType {
    private $em;
    private $emContable;
    
    public function __construct($em = null, $emContable = null) {
        $this->em = $em;
        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tipoContacto', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoContacto',
                    'required' => true,
                    'empty_value' => '-- Tipo contacto --',
                    'label' => 'Tipo de contacto',
                    'em' => $this->em,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('descripcionDatoContacto', null, array(
                    'required' => true,
                    'label' => 'Descripción',
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
            'data_class' => 'ADIF\ComprasBundle\Entity\DatoContacto'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_datocontacto';
    }

}
