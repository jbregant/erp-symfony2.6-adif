<?php
namespace ADIF\ContableBundle\Form\CajaChica;

use ADIF\ContableBundle\Form\CajaChica\PercepcionIIBBType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ComprobanteType extends AbstractType {
    
    private $emContable;
    
    public function __construct($emContable = null) {
        $this->emContable = $emContable;
    }
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('fecha', 'datetime', array(
			'required' => true,
			'label' => 'Fecha',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control  datepicker ',
			),
			'widget' => 'single_text',
			'format' => 'dd/MM/yyyy',
		))->add('puntoVenta', null, array(
			'required' => true,
			'label' => 'Puntoventa',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control ',
			),
		))->add('numero', null, array(
			'required' => true,
			'label' => 'N&uacute;mero',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control ',
			),
		))->add('observaciones', null, array(
			'required' => false,
			'label' => 'Observaciones',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control ',
			),
		))->add('letraComprobante', 'choice', array(
            'required' => true,
            'label' => 'Letra',
            'label_attr' => array('class' => 'control-label'),
            'attr' => array('class' => ' form-control choice '),
            'choices' => array(
            	'A' => 'A',
				'A con leyenda' => 'A con leyenda',
				'B' => 'B',
				'C' => 'C',
				'E' => 'E',
				'M' => 'M',
				'Y' => 'Y'
        	),
        	'empty_value' => '-- Letra --',
		))->add('montoIVA', null, array(
			'required' => true,
			'label' => 'IVA',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control currency ',
			),
		))->add('montoOtrosImpuestos', null, array(
			'required' => true,
			'label' => 'Otros impuestos',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control currency ',
			),
		))->add('montoPercepcionIVA', null, array(
			'required' => true,
			'label' => 'IVA',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control currency ',
			),
		))->add('montoPercepcionSUSS', null, array(
			'required' => true,
			'label' => 'SUSS',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control currency ',
			),
		))->add('montoPercepcionGanancias', null, array(
			'required' => true,
			'label' => 'Ganancias',
			'label_attr' => array(
				'class' => 'control-label',
			),
			'attr' => array(
				'class' => ' form-control currency ',
			),
		))->add('tipoComprobante', EntityType::clase, array(
			'class' => 'ADIF\ContableBundle\Entity\CajaChica\TipoComprobante',
			// 'empty_value' => '-- Tipo de comprobante --',
			'label' => 'Tipo de comprobante',
			'label_attr' => array('class' => 'control-label'),
            'em' => $this->emContable,
			'attr' => array(
				'class' => ' form-control choice ',
			),
			'required' => true
		))->add('proveedor', EntityType::clase, array(
			'class' => 'ADIF\ContableBundle\Entity\CajaChica\Proveedor',
			// 'empty_value' => '-- Proveedor --',
			'label' => 'Proveedor',
			'label_attr' => array('class' => 'control-label'),
            'em' => $this->emContable,
			'attr' => array(
				'class' => ' form-control choice ',
			),
			'required' => true
		));

		$builder->add('percepcionesIIBB', 'collection', array(
            'type' => new PercepcionIIBBType($this->emContable),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'ADIF\ContableBundle\Entity\CajaChica\Comprobante',
		));
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'adif_contablebundle_cajachica_comprobante';
	}
}
