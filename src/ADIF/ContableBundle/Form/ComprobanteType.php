<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fecha', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de factura',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('puntoVenta', null, array(
                    'required' => true,
                    'label' => 'Punto de venta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'Numero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('total', null, array(
                    'required' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency '),))
                ->add('montoValidacion', null, array(
                    'required' => true,
                    'label' => 'Montovalidacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency '),))
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'label' => 'Letra',
                    'required' => true,
                    'empty_value' => '-- Letra --',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                                ->orderBy('l.letra', 'ASC');
                    })
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'label' => 'Tipo de comprobante',
                    'required' => true,
                    'empty_value' => '-- Tipo de comprobante --',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                                ->orderBy('t.nombre', 'ASC');
                    })
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Comprobante'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobante';
    }

}
