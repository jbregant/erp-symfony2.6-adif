<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use ADIF\RecursosHumanosBundle\Form\DomicilioType;

class PersonaType extends AbstractType {
    
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
                ->add('apellido', null, array(
                    'required' => true,
                    'label' => 'Apellido',
                    'attr' => array('class' => ' form-control '),))
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'attr' => array('class' => ' form-control '),))
                ->add('sexo', 'choice', array(
                    'choices' => array('F' => 'Femenino','M' => 'Masculino',),
                    'required' => true,
                    'label' => 'Sexo',
                    'attr' => array('class' => ' form-control choice '),))
                ->add('nroDocumento', null, array(
                    'required' => true,
                    'label' => 'N&ordm; documento',
                    'attr' => array('class' => ' form-control '),))
                ->add('cuil', null, array(
                    'required' => true,
                    'label' => 'CUIL',
                    'attr' => array('class' => ' form-control '),))
                ->add('fechaNacimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de nacimiento',
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('idNacionalidad', null, array(
                    'required' => true,
                    'label' => 'Nacionalidad',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Nacionalidad',
                    'attr' => array('class' => ' form-control choice '),))
                ->add('telefono', null, array(
                    'required' => false,
                    'label' => 'Tel&eacute;fono',
                    'attr' => array('class' => ' form-control  number '),))
                ->add('celular', null, array(
                    'required' => false,
                    'label' => 'Celular',
                    'attr' => array('class' => ' form-control '),))
                ->add('email', null, array(
                    'required' => false,
                    'label' => 'Email',
                    'attr' => array('class' => ' form-control '),))
//                ->add('idDomicilio', 'entity', array(
//                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Domicilio',
//                    'attr' => array('class' => ' form-control choice '),))
                ->add('idEstadoCivil', EntityType::clase, array(
                    'label' => 'Estado civil',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\EstadoCivil',
                    'em' => $this->emRRHH,
                    'attr' => array('class' => ' form-control choice '),))
                ->add('idTipoDocumento', EntityType::clase, array(
                    'label' => 'Tipo de documento',
                    'em' => $this->emRRHH,
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoDocumento',
                    'attr' => array('class' => ' form-control choice '),
        ));
        
        $builder->add('domicilio', new DomicilioType($this->emRRHH));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Persona',
            'cascade_validation' => true
        ));
        //$resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_persona';
    }

}
