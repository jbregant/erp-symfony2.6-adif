<?php

namespace ADIF\ContableBundle\Form\Cobranza;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RetencionClienteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private $emContable;
    
    public function __construct($emContable = null, $emCompras = null) {       
        $this->emContable = $emContable;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fecha', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
//                ->add('cliente', 'entity', array(
//                    'required' => true,
//                    'label' => 'Cliente',                    
//                    'empty_value' => '-- Cliente --',
//                    'class' => 'ADIF\ComprasBundle\Entity\Cliente',
//                    'attr' => array('class' => ' form-control choice '),))  
                ->add('tipoImpuesto', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Impuesto',                    
                    'empty_value' => '-- Impuesto --',
                    'class' => 'ADIF\ContableBundle\Entity\RetencionClienteParametrizacion',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '),))                  
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number'),));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Cobranza\RetencionCliente'
        ));
        //$resolver->setRequired('entity_manager_conta');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cobranza_retencioncliente';
    }

}
