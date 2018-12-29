<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class PedidoInternoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaPedido', 'date', array(
                    'required' => true,
                    'label' => 'Fecha del pedido',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('descripcion', 'text', array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')
                ))
                ->add('justificacion', new JustificacionPedidoInternoType(), array(
                    'required' => false)
                )
                ->add('numeroReferencia', null, array(
                    'required' => false,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('centroCosto', EntityType::clase, array(
                    'label' => 'Centro de costo',
                    'class' => 'ADIF\ContableBundle\Entity\CentroCosto',
                    'attr' => array('class' => ' form-control choice '),
                    'label_attr' => array('class' => 'control-label'),
                    'required' => true,
                    'empty_value' => '-- Centro de costo --',
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(EntityRepository $er) {

                        $centroCostoOcultos = [10];

                        return $er->createQueryBuilder('cc')
                                ->where('cc.codigo >= (:centroCosto)')
                                ->setParameter('centroCosto', $centroCostoOcultos, Connection::PARAM_STR_ARRAY)
                                ->orderBy('cc.codigo', 'ASC');
                    }
                    
                    )
                )
                /*
                ->add('centroCosto', EntityType::clase, array(
                    'class' => 'ADIFContableBundle:CentroCosto',
                    'required' => true,
                    'label' => 'Centro de costo',
                    'empty_value' => '-- Centro de costo --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'),
                    'attr' => array('class' => 'form-control'),
                    'multiple' => false,
                    'em' => $options['entity_manager_contable'],
                    
                    'query_builder' => function(EntityRepository $er) {

                        $centroCostoOcultos = [10];

                        return $er->createQueryBuilder('cc')
                                ->where('cc.codigo >= (:centroCosto)')
                                ->setParameter('centroCosto', $centroCostoOcultos, Connection::PARAM_STR_ARRAY)
                                ->orderBy('cc.codigo', 'ASC');
                    }
                    
                    )
                     
                )
                 * 
                 */
                
                
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('renglonesPedidoInterno', 'collection', array(
                    'type' => new RenglonPedidoInternoType($options['entity_manager']),
                    'label' => 'Renglones del Pedido',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__renglon_pedido__')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\PedidoInterno'
        ));
        
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_contable');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_pedidointerno';
    }

}
