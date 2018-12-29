<?php
namespace ADIF\ContableBundle\Form\CajaChica;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PercepcionIIBBType extends AbstractType
{
    private $emContable;
    
    public function __construct($emContable = null) {
        $this->emContable = $emContable;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('monto', null, array(
            'required' => true,
            'label' => 'Monto',
            'label_attr' => array(
                'class' => 'control-label'
            ) ,
            'attr' => array(
                'class' => ' form-control '
            ) ,
        ))->add('fechaCreacion', 'datetime', array(
            'required' => true,
            'label' => 'Fechacreacion',
            'label_attr' => array(
                'class' => 'control-label'
            ) ,
            'attr' => array(
                'class' => ' form-control  datepicker '
            ) ,
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
        ))->add('fechaUltimaActualizacion', 'datetime', array(
            'required' => true,
            'label' => 'Fechaultimaactualizacion',
            'label_attr' => array(
                'class' => 'control-label'
            ) ,
            'attr' => array(
                'class' => ' form-control  datepicker '
            ) ,
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
        ))->add('fechaBaja', 'datetime', array(
            'required' => false,
            'label' => 'Fechabaja',
            'label_attr' => array(
                'class' => 'control-label'
            ) ,
            'attr' => array(
                'class' => ' form-control  datepicker '
            ) ,
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
        ))->add('jurisdiccion', EntityType::clase, array(
            'class' => 'ADIF\ContableBundle\Entity\Jurisdiccion',
            'em' => $this->emContable,
            'attr' => array(
                'class' => ' form-control choice '
            ) ,
        ))->add('comprobante', EntityType::clase, array(
            'class' => 'ADIF\ContableBundle\Entity\CajaChica\Comprobante',
            'em' => $this->emContable,
            'attr' => array(
                'class' => ' form-control choice '
            ) ,
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ CajaChica\PercepcionIIBB'
        ));
    }
    
    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cajachica_percepcioniibb';
    }
}
