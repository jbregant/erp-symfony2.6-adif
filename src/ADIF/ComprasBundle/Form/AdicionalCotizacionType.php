<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class AdicionalCotizacionType extends AbstractType {

    private $em;
    private $emContable;
    
    public function __construct($em = null, $emContable = null) {
        $this->em = $em;
        $this->emContable = $emContable;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tipoAdicional', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoAdicional',
                    'required' => true,
                    'label' => 'Tipo',
                    'empty_value' => '-- Tipo --',
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('signo', 'choice', array(
                    'choices' => array('+' => 'Suma (+)', '-' => 'Resta (-)',),
                    'required' => true,
                    'label' => 'Signo',
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('tipoValor', 'choice', array(
                    'choices' => array('$' => 'Monto', '%' => 'Porcentaje'),
                    'required' => true,
                    'label' => 'Tipo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('valor', null, array(
                    'required' => true,
                    'label' => 'Valor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control money-format',
                        'data-digits' => '4'
                    ))
                )
                ->add('alicuotaIva', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'required' => true,
                    'em' => $this->emContable,
                    'label' => 'Alicuota IVA',
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('tipoMoneda', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'required' => true,
                    'em' => $this->emContable,
                    'label' => 'Moneda',
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('tipoCambio', null, array(
                    'required' => true,
                    'label' => 'Tipo cambio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control money-format',
                        'data-digits' => '4'
                    ))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observación',
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
            'data_class' => 'ADIF\ComprasBundle\Entity\AdicionalCotizacion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_adicionalcotizacion';
    }

}
