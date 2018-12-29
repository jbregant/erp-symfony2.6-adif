<?php

namespace ADIF\ContableBundle\Form\Obras;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\ContableBundle\Entity\Aseguradora;
use ADIF\ContableBundle\Entity\TipoCobertura;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class PolizaSeguroDocumentoFinancieroType extends AbstractType {

    private $emContable;
    
    public function __construct($emContable = null) {

        $this->emContable = $emContable;
   }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * 
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numeroPoliza', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de p&oacute;liza',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('fechaInicio', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
               ->add('aseguradora', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Aseguradora',
                    'required' => true,
                    'empty_value' => 'normalizada',
                    'label' => 'Aseguradora',
                    'em' => $this->emContable,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'))
                )
               ->add('aseguradora2', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Aseguradora',
                    'required' => true,
                    'empty_value' => '-- Compania aseguradora --',
                    'label' => 'Aseguradora Norm.',
                    'em' => $this->emContable,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                                ->where('a.activo = 1')
                                ->orderBy('a.nombre','ASC');
                    })
                )
               ->add('EstadoPoliza', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EstadoPoliza',
                    'required' => true,
                    'empty_value' => '-- Denominaci&oacute;n --',
                    'label' => 'Estado',
                    'em' => $this->emContable,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                                ->orderBy('e.denominacion','ASC');
                    })
                )
               ->add('EstadoRevisionPoliza', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EstadoRevisionPoliza',
                    'required' => true,
                    'empty_value' => '-- Denominaci&oacute;n --',
                    'label' => 'Estado Revisi&oacute;n',
                    'em' => $this->emContable,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('er')
                                ->orderBy('er.denominacion','ASC');
                    })
                )
                ->add('tipoCobertura', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoCobertura',
                    'required' => true,
                    'empty_value' => '-- Tipo de cobertura --',
                    'label' => 'Tipo de cobertura',
                    'em' => $this->emContable,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('riesgoCubierto', null, array(
                    'required' => true,
                    'label' => 'Riesgo cubierto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroTramiteEnvio', null, array(
                    'required' => true,
                    'label' => 'N&ordm; tr&aacute;mite original',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroTramitePolizaGarantia', null, array(
                    'required' => false,
                    'label' => 'N&ordm; tr&aacute;mite P&oacute;liza Gt&iacute;a',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('montoAsegurado', null, array(
                    'required' => true,
                    'label' => 'Monto asegurado',
                    'label_attr' => array('class' => 'control-label currency'),
                    'attr' => array('class' => ' form-control currency'))
                );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\PolizaSeguroDocumentoFinanciero'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_polizasegurodocumentofinanciero';
    }

}
