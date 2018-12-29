<?php

namespace ADIF\ContableBundle\Form\ ConciliacionBancaria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RenglonConciliacionType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('fechaMovimientoBancario', 'datetime', array(
                    'required' => true,
                    'label' => 'Fechamovimientobancario',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        'widget' => 'single_text',
                        'format' => 'dd/MM/yyyy',                ))
                            

                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripcion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('numeroReferencia', null, array(
                    'required' => false,
                    'label' => 'Numeroreferencia',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('fechaCreacion', 'datetime', array(
                    'required' => true,
                    'label' => 'Fechacreacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        'widget' => 'single_text',
                        'format' => 'dd/MM/yyyy',                ))
                            

                ->add('fechaUltimaActualizacion', 'datetime', array(
                    'required' => true,
                    'label' => 'Fechaultimaactualizacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        'widget' => 'single_text',
                        'format' => 'dd/MM/yyyy',                ))
                            

                ->add('fechaBaja', 'datetime', array(
                    'required' => false,
                    'label' => 'Fechabaja',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        'widget' => 'single_text',
                        'format' => 'dd/MM/yyyy',                ))
                                ->add('conceptoConciliacion','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion',
                    'attr' => array('class' => ' form-control choice '),
                ))                ->add('estadoRenglonConciliacion','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion',
                    'attr' => array('class' => ' form-control choice '),
                ))                ->add('importacionConciliacion','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\ConciliacionBancaria\ImportacionConciliacion',
                    'attr' => array('class' => ' form-control choice '),
                ))                ->add('cheques','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\Cheque',
                    'attr' => array('class' => ' form-control choice '),
                ))                ->add('transferencias','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\TransferenciaBancaria',
                    'attr' => array('class' => ' form-control choice '),
                ))        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\ContableBundle\Entity\ ConciliacionBancaria\RenglonConciliacion'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_contablebundle_conciliacionbancaria_renglonconciliacion';
    }
}
