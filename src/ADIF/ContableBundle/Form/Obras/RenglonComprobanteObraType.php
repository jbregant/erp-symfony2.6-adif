<?php

namespace ADIF\ContableBundle\Form\Obras;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Repository\RegimenRetencionRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonComprobanteObraType extends AbstractType {

    private $emContable;
    private $emCompras;
    
    public function __construct($emContable = null, $emCompras = null) {

        $this->emContable = $emContable;
        $this->emCompras = $emCompras;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive no-editable'))
                )
                ->add('precioUnitario', null, array(
                    'required' => true,
                    'label' => 'Precio unitario',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive no-editable',
                        'data-digits' => '4')
                        )
                )
                ->add('bonificacionTipo', null, array(
                    'required' => true,
                    'label' => 'Tipo bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('bonificacionValor', null, array(
                    'required' => false,
                    'label' => 'Valor bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('montoNeto', null, array(
                    'label' => 'Neto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive no-editable',
                        'data-digits' => '4')
                        )
                )
                ->add('montoIva', null, array(
                    'label' => 'Monto IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive no-editable',
                        'data-digits' => '4')
                        )
                )
                ->add('regimenRetencionIVA', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'property' => 'denominacionCompleta',
                    'empty_value' => '-- Régimen retención IVA --',
                    'required' => false,
                    'em' => $this->emContable,
                    'label' => 'Régimen retención IVA',
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(RegimenRetencionRepository $er) {
                return $er->getRegimenes(ConstanteTipoImpuesto::IVA);
            })
                )
                ->add('regimenRetencionIIBB', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'property' => 'denominacionCompleta',
                    'empty_value' => '-- Régimen retención IIBB --',
                    'required' => false,
                    'em' => $this->emContable,
                    'label' => 'Régimen retención IIBB',
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(RegimenRetencionRepository $er) {
                return $er->getRegimenes(ConstanteTipoImpuesto::IIBB);
            })
                )
                ->add('regimenRetencionSUSS', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'property' => 'denominacionCompleta',
                    'empty_value' => '-- Régimen retención SUSS --',
                    'required' => false,
                    'em' => $this->emContable,
                    'label' => 'Régimen retención SUSS',
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(RegimenRetencionRepository $er) {
                return $er->getRegimenes(ConstanteTipoImpuesto::SUSS);
            })
                )
                ->add('regimenRetencionGanancias', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'property' => 'denominacionCompleta',
                    'empty_value' => '-- Régimen retención Ganancias --',
                    'required' => false,
                    'em' => $this->emContable,
                    'label' => 'Régimen retención Ganancias',
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(RegimenRetencionRepository $er) {
                return $er->getRegimenes(ConstanteTipoImpuesto::Ganancias);
            })
                )
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('alicuotaIva', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'label' => 'IVA',
                    'em' => $this->emContable,
                    'attr' => array('class' => 'form-control choice no-editable'),
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('ai')->orderBy('ai.valor', 'ASC');
            }
                ))
                ->add('idComprobante', 'hidden', array(
                    'required' => false,
                    'mapped' => false)
                )
                ->add('idRenglonComprobante', 'hidden', array(
                    'required' => false,
                    'mapped' => false)
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_rengloncomprobanteobra';
    }

}
