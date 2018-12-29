<?php

namespace ADIF\ComprasBundle\Form;

use ADIF\ComprasBundle\Form\CertificadoExencionType;
use ADIF\ComprasBundle\Form\ProveedorUTEType;
use ADIF\RecursosHumanosBundle\Form\CuentaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProveedorType extends AbstractType {

    private $esEdit;
    
    public function __construct($esEdit = null) {
        $this->esEdit = $esEdit;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) { 
        
        if ( $this->esEdit )
        {
            $aTipoMoneda = array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'required' => true,
                    'label' => 'Tipo de moneda',
                    'empty_value' => '-- Tipo moneda --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'));
        }
        else
        {
            $aTipoMoneda = array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'required' => true,
                    'label' => 'Tipo de moneda',
                    'empty_value' => '-- Tipo moneda --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice'));
        }
        
        if ( $this->esEdit )
        {
            $aCuentaContable = array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'required' => true,
                    'label' => 'Cuenta contable',
                    'empty_value' => '-- Cuenta contable --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('cc')
                                ->where('cc.esImputable = 1');
                    });
        }
        else
        {
            $aCuentaContable = array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'required' => true,
                    'label' => 'Cuenta contable',
                    'empty_value' => '-- Cuenta contable --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('cc')
                                ->where('cc.esImputable = 1');
                    });
        }
        
        
        $builder
                ->add('clienteProveedor', new ClienteProveedorType($options['entity_manager'], $options['entity_manager_contable'], $options['entity_manager_rrhh']), array(
                    'required' => false)
                )
                ->add('representanteLegal', null, array(
                    'required' => false,
                    'label' => 'Representante legal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('estadoProveedor', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\EstadoProveedor',
                    'label' => 'Estado',
                    'label_attr' => array('class' => 'control-label'),
                    'empty_value' => '-- Estado --',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('nacionalidad', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Nacionalidad',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Nacionalidad',
                    'empty_value' => '-- nacionalidad --',                    
                    'label_attr' => array('class' => 'control-label'),
                    //'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('rubros', EntityType::clase, array(
                    'required' => true,
                    'multiple' => true,
                    'class' => 'ADIF\ComprasBundle\Entity\Rubro',
                    'label' => 'Rubros',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager'],
                    'empty_value' => '-- Estado --',
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('contactosProveedor', 'collection', array(
                    'type' => new ContactoProveedorType($options['entity_manager'], $options['entity_manager_contable']),
                    'label' => 'Contactos',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__contacto_proveedor__',
                    //'options' => array( 'em' => $options['entity_manager'])
                    )
                )
                ->add('cuenta', new CuentaType($options['entity_manager_rrhh']), array(
                    'required' => false)
                )
                ->add('cais', 'collection', array(
                    'type' => new CodigoAutorizacionImpresionProveedorType(),
                    'label' => 'CAI',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__cai_proveedor__',
                    //'em' => $options['entity_manager']
                    )
                )
                ->add('tipoPago', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoPago',
                    'required' => false,
                    'label' => 'Tipo de pago',
                    'empty_value' => '-- Tipo pago --',
                    'label_attr' => array('class' => 'control-label'),
                    //'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('tipoMoneda', EntityType::clase, $aTipoMoneda
                )
                ->add('cuentaContable', EntityType::clase, $aCuentaContable
                )
                ->add('pasibleRetencionIVA', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('certificadoExencionIVA', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('pasibleRetencionGanancias', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('certificadoExencionGanancias', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('pasibleRetencionIngresosBrutos', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('certificadoExencionIngresosBrutos', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('pasibleRetencionSUSS', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('certificadoExencionSUSS', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('evaluacionProveedor', new EvaluacionProveedorType($options['entity_manager'], $options['entity_manager_contable']), array(
                    'required' => false,
                    'label' => 'Evaluación')
                )
                ->add('observacionCalificacion', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('esUTE', null, array(
                    'required' => false,
                    'label' => 'Es UTE',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('proveedoresUTE', 'collection', array(
                    'type' => new ProveedorUTEType($options['entity_manager']),
                    'label' => 'Proveedores',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__proveedor_ute__',
                    //'em' => $options['entity_manager']
                    )
                )
				->add('iibbCaba', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\IibbCaba',
                    'empty_value' => '-- Estado --',
					'label' => 'Grupo',
                    'attr' => array('class' => ' form-control choice '),
					'required' => false,
                    'em' => $options['entity_manager'],
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('i')
							->where('i.esProveedor = 1')
							->orderBy('i.grupo', 'ASC'); 
						}
					)
				)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\Proveedor'
        ));
        
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_rrhh');
        $resolver->setRequired('entity_manager_contable');
         
         
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_proveedor';
    }

}
