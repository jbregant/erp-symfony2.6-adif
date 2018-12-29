<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EvaluacionAspectoProveedorType extends AbstractType {
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
                ->add('aspectoEvaluacion', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\AspectoEvaluacion',
                    'property' => 'denominacionAspectoEvaluacion',
                    'read_only' => true,
                    'label' => 'Aspecto',
                    'em' => $this->em,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('valorAlcanzado', null, array(
                    'required' => true,
                    'label' => 'Valor alcanzado',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control valor-alcanzado hidden'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_evaluacionaspectoproveedor';
    }

}
