<?php
namespace ADIF\ContableBundle\Form\CajaChica;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RendicionType extends AbstractType
{
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('fecha', 'datetime', array(
            'required' => true,
            'label' => 'Fecha',
            'label_attr' => array(
                'class' => 'control-label'
            ) ,
            'attr' => array(
                'class' => ' form-control  datepicker '
            ) ,
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
        ))->add('numero', null, array(
            'required' => true,
            'label' => 'N&uacute;mero',
            'label_attr' => array(
                'class' => 'control-label'
            ) ,
            'attr' => array(
                'class' => ' form-control numeric'
            ) ,
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\CajaChica\Rendicion'
        ));
    }
    
    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cajachica_rendicion';
    }
}
