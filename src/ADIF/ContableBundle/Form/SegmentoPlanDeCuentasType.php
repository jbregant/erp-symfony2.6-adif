<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


/**
 * SegmentoPlanDeCuentasType
 * 
 * @author Manuel Becerra
 * created 24/06/2014
 * 
 */
class SegmentoPlanDeCuentasType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private $emContable;
    
    public function __construct($emContable = null, $emCompras = null) {

        $this->emContable = $emContable;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                /* Tipo Segmento */
                ->add('tipoSegmento', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoSegmentoPlanDeCuentas',
                    'required' => true,
                    'empty_value' => '-- Tipo segmento --',
                    'label' => 'Tipo segmento',
                    'em' => $this->emContable,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice '))
                )
                /* Posición */
                ->add('posicion', 'integer', array(
                    'required' => true,
                    'read_only' => true,
                    'label' => 'Posición',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control',
                        'tabindex' => '5'))
                )
                /* Longitud */
                ->add('longitud', 'integer', array(
                    'required' => true,
                    'label' => 'Longitud',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control longitud',
                        'tabindex' => '5'))
                )
                /* Separador */
                ->add('separador', 'text', array(
                    'required' => false,
                    'label' => 'Separador',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control separador',
                        'tabindex' => '5'))
                )
                /* Indica Centro de Costos */
                ->add('indicaCentroDeCosto', 'checkbox', array(
                    'required' => false,
                    'label' => 'Centro de costo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control',
                        'tabindex' => '5')
                        )
                )
                /* Denominación */
                ->add('denominacionSegmento', 'text', array(
                    'required' => false,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'placeholder' => 'Escriba la denominación aquí.',
                        'class' => 'form-control',
                        'tabindex' => '5'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\SegmentoPlanDeCuentas'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_segmentoplandecuentas';
    }

}
