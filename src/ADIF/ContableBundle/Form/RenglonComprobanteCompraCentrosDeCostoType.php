<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonComprobanteCompraCentrosDeCostoType extends AbstractType {

    private $emCompras;
    private $emContable;
    
    public function __construct($emCompras = null, $emContable = null) {
        $this->emCompras = $emCompras;
        $this->emContable = $emContable;
        //parent::__construct();
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('centroDeCosto', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\CentroCosto',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice ')))
                ->add('porcentaje', null, array(
                    'required' => true,
                    'attr' => array('class' => ' form-control percentage '),));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_rengloncomprobantecompra_centrosdecosto';
    }

}
