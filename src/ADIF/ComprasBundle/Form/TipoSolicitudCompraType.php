<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class TipoSolicitudCompraType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('denominacionTipoSolicitudCompra', null, array(
                    'required' => true,
                    'label' => 'Denominación',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')          
                ))
                            

                ->add('descripcionTipoSolicitudCompra', null, array(
                    'required' => false,
                    'label' => 'Descripción',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')               
                ))
                
                
                ->add('tipoImportancia', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoImportancia',
                    'required' => true,
                    'attr' => array(
                        'class' => ' form-control choice '),
                    'em' => $options['entity_manager']
                ))
            ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\ComprasBundle\Entity\TipoSolicitudCompra'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_comprasbundle_tiposolicitudcompra';
    }
}
