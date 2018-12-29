<?php
namespace ADIF\ContableBundle\Form;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Form\RenglonComprobanteCompraType;
use ADIF\ContableBundle\Form\RenglonPercepcionType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteAjusteType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('proveedor', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'B&uacute;squeda de proveedor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('proveedor_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'mapped' => false,
                    'label' => 'Proveedor',
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
				->add('cliente', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'B&uacute;squeda del cliente',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
				->add('cliente_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'mapped' => false,
                    'label' => 'Cliente',
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('proveedor_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
				->add('cliente_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('idProveedor', 'hidden', array(
                    'required' => true)
                )
				->add('idCliente', 'hidden', array(
                    'required' => true)
                )
				->add('idComprobante', 'hidden', array(
                    'required' => true)
                )
                ->add('fechaComprobante', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
				->add('detalle', 'hidden', array(
                    'required' => false)
                )
                ->add('observaciones', 'text', array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('total', 'integer', array(
                    'required' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
					'precision'	=> 2,
                    'attr' => array('class' => ' form-control currency '))
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
					'mapped' => false,
                    'label' => 'Tipo de comprobante',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {

					$comprobantesValidos = [
						ConstanteTipoComprobanteCompra::NOTA_CREDITO,
                        ConstanteTipoComprobanteCompra::NOTA_DEBITO,
					];

					return $er->createQueryBuilder('t')
                        ->where('t.id IN (:nombre)')
                        ->setParameter('nombre', $comprobantesValidos, Connection::PARAM_STR_ARRAY)
                        ->orderBy('t.nombre', 'ASC');
					})
                )
				->add('letraComprobante', 'text', array(
					'disabled' => true,
					'mapped' => false,
					'required' => false,
                    'data' => 'Y',
                    'label' => 'Letra',
                    'attr' => array('class' => ' form-control '))
                )
				;
            
        
        
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\ContableBundle\Entity\ComprobanteAjuste'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_contablebundle_comprobanteajuste';
    }
}
