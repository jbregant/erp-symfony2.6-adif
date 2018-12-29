<?php

namespace ADIF\ContableBundle\Form;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class TransferenciaBancariaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numeroTransferencia', null, array(
                    'required' => true,
                    'read_only' => true,
                    'label' => 'N&ordm; transferencia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('estadoPago', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EstadoPago',
                    'required' => true,
                    'empty_value' => '-- Elija un estado --',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => 'form-control choice'),
                    'query_builder' => function(EntityRepository $er) {

                        $estadosValidos = [
                            ConstanteEstadoPago::ESTADO_A_GENERAR,
                            ConstanteEstadoPago::ESTADO_A_LA_FIRMA,
                            ConstanteEstadoPago::ESTADO_EN_CARTERA,
                            ConstanteEstadoPago::ESTADO_RETIRADO,
                            ConstanteEstadoPago::ESTADO_ACREDITADA,
                            ConstanteEstadoPago::ESTADO_PAGO_ANULADO
                        ];

                        return $er->createQueryBuilder('ep')
                                ->where('ep.denominacionEstado IN (:denominacionEstado)')
                                ->setParameter('denominacionEstado', $estadosValidos, Connection::PARAM_STR_ARRAY)
                                ->orderBy('ep.id', 'ASC');
                    })
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\TransferenciaBancaria'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_transferenciabancaria';
    }

}
