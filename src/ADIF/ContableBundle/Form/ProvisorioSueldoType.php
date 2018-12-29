<?php

namespace ADIF\ContableBundle\Form;

use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaPresupuestariaEconomica;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProvisorioSueldoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaProvisorio', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('cuentaPresupuestariaEconomica', EntityType::clase, array(
                    'label' => 'Cuenta presupuestaria',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(EntityRepository $er) {

                return $er->createQueryBuilder('c')
                        ->where('c.codigoInterno = :codigoInterno')
                        ->setParameter('codigoInterno', ConstanteCodigoInternoCuentaPresupuestariaEconomica::PROVISORIO_SUELDOS);
            })
                )
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency '))
                )
                ->add('detalle', null, array(
                    'required' => true,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ProvisorioSueldo'
        ));
        $resolver->setRequired('entity_manager_contable');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_provisoriosueldo';
    }

}
