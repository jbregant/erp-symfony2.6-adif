<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\ContableBundle\Repository\TipoResponsableRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\TipoResponsable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DatosImpositivosType extends AbstractType {

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
                ->add('condicionIVA', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoResponsable',
                    'required' => true,
                    'label' => 'Condición',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    //'em' => $this->emContable,
                    'query_builder' => function(TipoResponsableRepository $er) {
                        return $er->getTiposResponsableByTipoImpuesto(ConstanteTipoImpuesto::IVA);
                    })
                )
                ->add('exentoIVA', null, array(
                    'required' => false,
                    'label' => 'Exento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('observacionExentoIVA', null, array(
                    'required' => false,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('condicionGanancias', EntityType::clase ,array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoResponsable',
                    //'em' => $this->emContable,
                    'required' => true,
                    'label' => 'Condición',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    'query_builder' => function(TipoResponsableRepository $er) {
                        return $er->getTiposResponsableByTipoImpuesto(ConstanteTipoImpuesto::Ganancias);
                    })
                )
                ->add('exentoGanancias', null, array(
                    'required' => false,
                    'label' => 'Exento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                            /*
                ->add('numeroIngresosBrutos', 'entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\NumeroIngresosBrutos',
                    'required' => false,
                    'label' => 'N&ordm; ingresos brutos',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                 
                             * 
                             */           
                ->add('numeroIngresosBrutos', null, array(
                    'required' => false,
                    'label' => 'N&ordm; ingresos brutos',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                            
                ->add('condicionIngresosBrutos', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoResponsable',
                    'required' => true,
                    'label' => 'Condición',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    //'em' => $this->emContable,
                    'query_builder' => function(TipoResponsableRepository $er) {
                        return $er->getTiposResponsableByTipoImpuesto(ConstanteTipoImpuesto::IIBB);
                    })
                )
                ->add('exentoIngresosBrutos', null, array(
                    'required' => false,
                    'label' => 'Exento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('condicionSUSS', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoResponsable',
                    'required' => true,
                    'label' => 'Condición',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    //'em' => $this->emContable,
                    'query_builder' => function(TipoResponsableRepository $er) {
                        return $er->getTiposResponsableByTipoImpuesto(ConstanteTipoImpuesto::SUSS);
                    })
                )
                ->add('exentoSUSS', null, array(
                    'required' => false,
                    'label' => 'Exento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('convenioMultilateralIngresosBrutos', new ConvenioMultilateralType($this->em), array(
                    'required' => true)
                )
                ->add('situacionClienteProveedor', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\SituacionClienteProveedor',
                    'required' => false,
                    'empty_value' => '-- Elija un c&oacute;digo de situaci&oacute;n --',
                    'label' => 'C&oacute;digo de situaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('fechaUltimaActualizacionCodigoSituacion', 'date', array(
                    'required' => false,
                    'read_only' => true,
                    'label' => 'Última actualización',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('tieneRiesgoFiscal', null, array(
                    'required' => false,
                    'label' => 'Tiene riesgo fiscal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('fechaUltimaActualizacionRiesgoFiscal', 'date', array(
                    'required' => false,
                    'read_only' => true,
                    'label' => 'Última actualización',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('incluyeMagnitudesSuperadas', null, array(
                    'required' => false,
                    'label' => 'Incluye magnitudes superadas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('fechaUltimaActualizacionIncluyeMagnitudesSuperadas', 'date', array(
                    'required' => false,
                    'read_only' => true,
                    'label' => 'Última actualización',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('tieneProblemasAFIP', null, array(
                    'required' => false,
                    'label' => 'Tiene problemas AFIP',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('fechaUltimaActualizacionTieneProblemasAFIP', 'date', array(
                    'required' => false,
                    'read_only' => true,
                    'label' => 'Última actualización',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\DatosImpositivos'
        ));
        
        //$resolver->setRequired('entity_manager');
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_datosimpositivos';
    }

}
