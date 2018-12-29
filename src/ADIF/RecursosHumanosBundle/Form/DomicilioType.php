<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DomicilioType extends AbstractType {

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
                ->add('calle', null, array(
                    'required' => true,
                    'label' => 'Calle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&ordm;',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('piso', null, array(
                    'required' => false,
                    'label' => 'Piso',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('depto', null, array(
                    'required' => false,
                    'label' => 'Dto.',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('codPostal', null, array(
                    'required' => false,
                    'label' => 'CP',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                
                ->add('localidad', EntityType::clase, array(
                    'label' => 'Localidad',
                    'required' => true,
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Localidad',
                    'attr' => array('class' => ' form-control choice '),
                    'label_attr' => array('class' => 'control-label'),
                    'required' => true,
                    'empty_value' => '-- Localidad --',
                    //'em' => $this->emRRHH,
                    'auto_initialize' => false)
                )
                ->add('idProvincia', EntityType::clase, array(
                    'label' => 'Provincia',
                    'required' => true,
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Provincia',
                    'attr' => array('class' => ' form-control choice '),
                    'label_attr' => array('class' => 'control-label'),
                    'required' => true,
                    'empty_value' => '-- Provincia --',
                    'mapped' => false,
                    'em' => $this->emRRHH,
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('p')
                        ->orderBy('p.nombre', 'ASC');
            },));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver
                ->setDefaults(array(
                    'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Domicilio',
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_domicilio';
    }

}
