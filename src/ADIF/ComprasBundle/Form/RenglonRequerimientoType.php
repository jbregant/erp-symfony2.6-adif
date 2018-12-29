<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RenglonRequerimientoType extends AbstractType {

    private $em;
    
    public function __construct($em = null) {
        $this->em = $em;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('requerimiento', 'entity', array(
                    'class' => 'ADIF\ComprasBundle\Entity\Requerimiento',
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('renglonSolicitudCompra', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\RenglonSolicitudCompra',
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\RenglonRequerimiento'
        ));
        //$resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_renglonrequerimiento';
    }

}
