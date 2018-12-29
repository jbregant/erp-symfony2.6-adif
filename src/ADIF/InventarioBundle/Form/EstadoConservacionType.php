<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstadoConservacionType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominaci&oacute;n',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('denominacionCorta', null, array(
                    'required' => false,
                    'label' => 'Denominaci&oacute;n Corta',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                /*->add('idEmpresa', null, array(
                    'required' => true,
                    'label' => 'Idempresa',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))*/
                            

                ->add('habilitadoMaterialNuevo', null, array(
                    'required' => true,
                    'label' => 'Material Nuevo',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('habilitadoMaterialProducido', null, array(
                    'required' => true,
                    'label' => 'Material Producido',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('habilitadoMaterialRodante', null, array(
                    'required' => true,
                    'label' => 'Material Rodante',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('habilitadoActivoLineal', null, array(
                    'required' => true,
                    'label' => 'Activo Lineal',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                /*->add('idUsuarioCreacion', null, array(
                    'required' => false,
                    'label' => 'Idusuariocreacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))
                            

                ->add('idUsuarioUltimaModificacion', null, array(
                    'required' => false,
                    'label' => 'Idusuarioultimamodificacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))*/
                        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_estadoconservacion';
    }
}
