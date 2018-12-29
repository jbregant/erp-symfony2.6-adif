<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EmpleadoTipoContratoType extends AbstractType {
    
    private $emRRHH;
    
    public function __construct($emRRHH = null) {

        $this->emRRHH = $emRRHH;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaDesde', 'date', array(
                    'required' => true,
                    'label' => 'Inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('fechaHasta', 'date', array(
                    'required' => false,
                    'label' => 'Fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
//                ->add('empleado', 'entity', array(
//                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Empleado',
//                    'attr' => array('class' => ' form-control choice '),
//                    'mapped' => false))
                ->add('descripcion', null, array(
                    'attr' => array('class' => ' form-control'),
                    'label' => 'Descripci&oacute;n',))
                ->add('tipoContrato', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoContrato',
                    'attr' => array('class' => ' form-control choice '),
                    'label' => 'Tipo de contrataci&oacute;n',
                    'em' => $this->emRRHH,
                    'query_builder' => 
                    function(\Doctrine\ORM\EntityRepository $er) {
                        return $er->createQueryBuilder('t')->orderBy('t.codigo', 'ASC');
                    }
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\EmpleadoTipoContrato'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_empleadotipocontrato';
    }

}
