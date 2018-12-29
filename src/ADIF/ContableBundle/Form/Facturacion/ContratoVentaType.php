<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ContratoVentaType extends AbstractType {
    
    private $emContable;
    
    public function __construct($emContable = null, $emCompras = null) {       
        $this->emContable = $emContable;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('claseContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\ClaseContrato',
                    'attr' => array('class' => 'form-control choice no-editable'),
                    'em' => $this->emContable,
                    'query_builder' => function(EntityRepository $er) {

                        $clasesContratoOcultas = [
                            ConstanteClaseContrato::VENTA_GENERAL
                        ];

                        return $er->createQueryBuilder('cc')
                                ->where('cc.codigo NOT IN (:codigo)')
								->andWhere('cc.activo = TRUE')
                                ->setParameter('codigo', $clasesContratoOcultas, Connection::PARAM_STR_ARRAY)
                                ->orderBy('cc.denominacion', 'ASC');
                    })
                )
                ->add('categoriaContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato',
                    'label' => 'Tipo contrato',
                    'em' => $this->emContable,
                    'attr' => array('class' => 'form-control hidden no-editable'))
                )
                ->add('numeroContrato', null, array(
                    'required' => true,
                    'label' => 'N&ordm; contrato',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('numeroOnabe', null, array(
                    'required' => false,
                    'label' => 'N&ordm; contrato Onabe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('numeroCarpeta', null, array(
                    'required' => false,
                    'label' => 'N&ordm; carpeta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('cliente', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de cliente',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control no-editable'))
                )
                ->add('cliente_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Cliente',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('cliente_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('idCliente', 'hidden', array(
                    'required' => true)
                )
                ->add('fechaInicio', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaFin', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('contratoOrigen', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\Contrato',
                    'empty_value' => '-- Contrato origen --',
                    'label' => 'Contrato',
                    'em' => $this->emContable,
                    'attr' => array('class' => 'form-control hidden no-editable'))
                )
                ->add('tipoMoneda', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'label' => 'Tipo moneda',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice no-editable'))
                )
                ->add('importeTotal', null, array(
                    'required' => true,
                    'label' => 'Importe total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control money-format changeable text-right no-editable'))
                )
                ->add('diaVencimiento', null, array(
                    'required' => false,
                    'label' => 'D&iacute;a de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control number no-editable'))
                )
                ->add('porcentajeTasaInteresMensual', null, array(
                    'required' => true,
                    'label' => 'Tasa inter&eacute;s mensual',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control percentage text-right'))
                )
                ->add('estadoContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\EstadoContrato',
                    'label' => 'Estado',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('calculaIVA', null, array(
                    'required' => false,
                    'label' => 'Calcula IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control no-editable'))
                )
                ->add('esExportacion', null, array(
                    'required' => false,
                    'label' => 'Es exportaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control no-editable'))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('ciclosFacturacion', 'collection', array(
                    'type' => new CicloFacturacionType($this->emContable),
                    'label' => 'Ciclos de facturaci&oacute;n',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__ciclo_facturacion__')
                )
                ->add('polizasSeguro', 'collection', array(
                    'type' => new PolizaSeguroContratoType(),
                    'label' => 'P&oacute;lizas de seguro',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__poliza__')
                )
                ->add('numeroLicitacion', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'N&ordm; licitaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('fechaApertura', 'date', array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Fecha de apertura',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('numeroInmueble', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'N&ordm; inmueble',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('fechaDesocupacion', 'date', array(
                    'required' => false,
                    'label' => 'Fecha de desocupaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
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
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\ContratoVenta'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_contrato';
    }

}
