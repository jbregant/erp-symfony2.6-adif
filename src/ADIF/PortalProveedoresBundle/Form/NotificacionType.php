<?php
namespace ADIF\PortalProveedoresBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificacionType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('titulo', null, array(
                    'required' => true,
                    'label' => 'T&iacute;tulo',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                
                ))
                            

                ->add('fechaDesde', 'date', array(
                    'required' => true,
                    'label' => 'Fecha Desde',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',                
                ))
                            

                ->add('fechaHasta', 'date', array(
                    'required' => true,
                    'label' => 'Fecha Hasta',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',                
                ))
                            

                ->add('autor', null, array(
                    'required' => true,
                    'label' => 'Autor',                    
                    'label_attr' => array('class' => 'hidden'),
                    'attr' => array('class' => ' hidden '),                
                ))
                            

                ->add('mensaje', null, array(
                    'required' => true,
                    'label' => 'Mensaje',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control msj'),                
                ))
                            

                ->add('estadoId', null, array(
                    'required' => true,
                    'label' => 'Estado',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                
                ))
                            

                ->add('idUsuarioCreacion', null, array(
                    'required' => false,
                    'label' => 'Usuario Creaci&oacute;n',                    
                    'label_attr' => array('class' => 'control-label hidden'),
                    'attr' => array('class' => ' form-control  number hidden'),                
                ))
                            

                ->add('idUsuarioUltimaModificacion', null, array(
                    'required' => false,
                    'label' => 'Usuario &Uacute;ltima Modificaci&oacute;n',                    
                    'label_attr' => array('class' => 'control-label hidden'),
                    'attr' => array('class' => ' form-control  number  hidden'),                
                ))
                
                ->add('estadoNotificacion','entity', array(
                    'class' => 'ADIF\PortalProveedoresBundle\Entity\EstadoNotificacion',
                    'label' => 'Estado Notificaci&oacute;n', 
                    'label_attr' => array('class' => 'control-label hidden', 'id' => 'notificacion'),
                    'attr' => array('class' => ' form-control choice'),
                ))        
                ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\PortalProveedoresBundle\Entity\Notificacion'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_portalproveedoresbundle_notificacion';
    }
}
