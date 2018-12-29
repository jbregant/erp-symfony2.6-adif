<?php

namespace ADIF\PortalProveedoresBundle\Form;

use ADIF\PortalProveedoresBundle\Entity\ObservacionEvaluacion;
use ADIF\PortalProveedoresBundle\Entity\ProveedorEvaluacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProveedorEvaluacionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('observacionEvaluacion', 'collection', array(
                  'type' => new ObservacionEvaluacionType(), ))
                 // 'options' => array('label' => false), ))
                
                ->add('submit', 'submit', array('label' => 'Enviar datos en Observaci&oacute;n') );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\PortalProveedoresBundle\Entity\ProveedorEvaluacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adif_portalProveedoresBundle_proveedorEvaluacion';
    }

}
