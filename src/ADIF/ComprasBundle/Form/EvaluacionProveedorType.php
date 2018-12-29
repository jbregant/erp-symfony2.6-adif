<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EvaluacionProveedorType extends AbstractType {
    
    private $em;
    private $emContable;
    
    public function __construct($em = null, $emContable = null) {
        $this->em = $em;
        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('proveedor', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Proveedor',
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('fechaEvaluacion', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de evaluación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('calificacionFinal', 'text', array(
                    'required' => false,
                    'mapped' => false,
                    'read_only' => true,
                    'label' => 'Calificación final',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('evaluacionesAspectos', 'collection', array(
                    'type' => new EvaluacionAspectoProveedorType($this->em, $this->emContable),
                    'required' => false,
                    'allow_add' => true,
                    'label' => 'Aspecto de evaluación')
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\EvaluacionProveedor'
        ));
        //$resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_evaluacionproveedor';
    }

}
