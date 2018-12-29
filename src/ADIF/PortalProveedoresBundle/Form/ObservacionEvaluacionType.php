<?php

namespace ADIF\PortalProveedoresBundle\Form;

use ADIF\BaseBundle\Entity\ObservacionEvaluacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObservacionEvaluacionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

                ->add('proveedorEvaluacion', null, array(
                    'required' => false,
                    'mapped' => false,
                    'property'=> 'id',
                    'label' => 'id_proveedor_evaluacion', 
                    'label_attr' => array('class' => 'hidden'),
                    'attr' => array('class' => ' hidden '),                ))

                ->add('tipoObservacion', null, array(
                    'required' => false,
                    'mapped' => false,
                    'property'=> 'id',
                    'label' => 'Tipo de Observacion',  
                    'label_attr' => array('class' => 'hidden'),
                    'attr' => array('class' => ' hidden '),                ))

                ->add('observaciones', null, array(
                    'required' => false,
                   // 'mapped' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\PortalProveedoresBundle\Entity\ObservacionEvaluacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adif_portalProveedoresBundle_observacionEvaluacion';
    }

}
