<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoNetCash;

class NetCashType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaEntrega', 'date', array(
                    'required' => false,
                    'label' => 'Fecha entrega',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('fechaPago', 'date', array(
                    'required' => false,
                    'label' => 'Fecha pago',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('estadoNetCash', 'entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\EstadoNetCash',
                    'label' => 'Estado',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        $estadosValidos = [
                            ConstanteEstadoNetCash::ESTADO_ENVIADO,
                            ConstanteEstadoNetCash::ESTADO_GENERADO
                        ];
            
                        return $er->createQueryBuilder('e')
                                ->where('e.denominacion IN (:denominacionEstado)')
                                ->setParameter('denominacionEstado', $estadosValidos, Connection::PARAM_STR_ARRAY);
                        }
                    ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\NetCash'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_netcash';
    }

}
