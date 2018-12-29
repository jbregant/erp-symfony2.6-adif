<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ClienteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('clienteProveedor', new ClienteProveedorType($options['entity_manager_compras'], $options['entity_manager_contable'], $options['entity_manager_hhrr']), array(
                    'required' => false)
                )
                ->add('representanteLegal', null, array(
                    'required' => false,
                    'label' => 'Representante legal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('tipoMoneda', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'required' => true,
                    'label' => 'Tipo de moneda',
                    'empty_value' => '-- Tipo moneda --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    'em' => $options['entity_manager_contable'])
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'required' => true,
                    'label' => 'Cuenta contable',
                    'empty_value' => '-- Cuenta contable --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    'em' => $options['entity_manager_contable'])
                )
                ->add('pasiblePercepcionIngresosBrutos', null, array(
                    'required' => false,
                    'label' => 'Pasible percepción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('certificadoExencionIVA', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('certificadoExencionGanancias', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('certificadoExencionIngresosBrutos', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('certificadoExencionSUSS', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('estadoCliente', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\EstadoCliente',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager_compras'])
                )
				->add('iibbCaba', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\IibbCaba',
					'label' => 'Grupo',
                    'attr' => array('class' => ' form-control choice '),
					'required' => false,
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('i')
							->where('i.esProveedor = 0')
							->orderBy('i.grupo', 'ASC'); 
						},
                    'em' => $options['entity_manager_compras']
					)
				)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\Cliente'
        ));
        
        $resolver->setRequired('entity_manager_compras');
        $resolver->setRequired('entity_manager_contable');
        $resolver->setRequired('entity_manager_hhrr');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_cliente';
    }

}
