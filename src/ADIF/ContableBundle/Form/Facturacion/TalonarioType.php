<?php

namespace ADIF\ContableBundle\Form\ Facturacion;

use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Form\CodigoAutorizacionImpresionTalonarioType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TalonarioType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'label' => 'Tipo de comprobante',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
            
                        $comprobantesValidos = [
                            ConstanteTipoComprobanteVenta::FACTURA,
                            ConstanteTipoComprobanteVenta::NOTA_CREDITO,
                            ConstanteTipoComprobanteVenta::NOTA_DEBITO,
                            ConstanteTipoComprobanteVenta::RECIBO
                        ];

                        return $er->createQueryBuilder('t')
                                ->where('t.id IN (:nombre)')
                                ->setParameter('nombre', $comprobantesValidos, Connection::PARAM_STR_ARRAY)
                                ->orderBy('t.nombre', 'ASC');
                    }, 
                    'em' => $options['entity_manager']
                    )
                )
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'label' => 'Letra',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {

                        $letrasValidas = [
                            ConstanteLetraComprobante::A,
                            ConstanteLetraComprobante::B,
                            ConstanteLetraComprobante::E
                        ];

                        return $er->createQueryBuilder('l')
                                ->where('l.letra IN (:letra)')
                                ->setParameter('letra', $letrasValidas, Connection::PARAM_STR_ARRAY)
                                ->orderBy('l.letra', 'ASC');
                    },
                    'em' => $options['entity_manager']
                    )
                )
                ->add('puntoVenta', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\PuntoVenta',
                    'required' => true,
                    'label' => 'Punto de venta',
                    'empty_value' => '-- Punto de venta --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                                ->where('p.generaComprobanteElectronico = 0')
                                ->orderBy('p.numero', 'ASC');
                    },
                    'em' => $options['entity_manager']
                    )
                )
                ->add('numeroDesde', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('numeroHasta', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('codigoAutorizacionImpresionTalonario', new CodigoAutorizacionImpresionTalonarioType(), array('required' => true)
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\Talonario'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_talonario';
    }

}
