<?php

namespace ADIF\ComprasBundle\Form;

use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoBienEconomico;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonSolicitudCompraType extends AbstractType {

    /**
     *
     * @var type 
     */
    private $securityContext;
    private $emCompras;
    /**
     * 
     * @param \ADIF\ComprasBundle\Form\SecurityContext $securityContext
     */
    public function __construct(SecurityContext $securityContext, $emCompras = null) {
        $this->securityContext = $securityContext;
        $this->emCompras = $emCompras;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('renglonPedidoInterno', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\RenglonPedidoInterno',
                    'required' => false,
                    'em' => $this->emCompras,
                    'attr' => array('class' => 'hidden')
                ))
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))
                ->add('cantidadSolicitada', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control changeable currency-format'),
                ))
                ->add('bienEconomico', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\BienEconomico',
                    'required' => true,
                    'label' => 'Bien económico',
                    'empty_value' => '-- Bien Económico --',
                    'em' => $this->emCompras,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {

                        return $er->createQueryBuilder('be')
                                ->join('be.estadoBienEconomico', 'e')
                                ->where('e.denominacionEstadoBienEconomico =  :denominacionEstadoBienEconomico')
                                ->setParameter('denominacionEstadoBienEconomico', ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_ACTIVO)
                                ->orderBy('be.denominacionBienEconomico', 'ASC');
                    })
                )
                ->add('prioridad', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Prioridad',
                    'required' => true,
                    'label' => 'Prioridad',
                    'em' => $this->emCompras,
                    'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => ' form-control choice '),
                ))
                ->add('unidadMedida', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\UnidadMedida',
                    'required' => true,
                    'label' => 'Unidad',
                    'em' => $this->emCompras,
                    'label_attr' => array('class' => 'control-label'), 'attr' => array('class' => ' form-control choice '),
                ))
                ->add('justiprecioUnitario', null, array(
                    'required' => true,
                    'label' => 'Justiprecio unit.',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control changeable money-format',
                        'data-digits' => '4'
                    ),
                ))
                ->add('justiprecioTotal', null, array(
                    'required' => false,
                    'mapped' => false,
                    'read_only' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control money-format',
                        'data-digits' => '4'
                    ),
                ))
                ->add('especificacionTecnica', new EspecificacionTecnicaType(), array(
                    'required' => false)
                )
        ;

        // Si el usuario puede administrar pedidos internos
        if (true === $this->securityContext->isGranted('ROLE_COMPRAS_ADMINISTRA_PEDIDO_INTERNO') //
                && false === $this->securityContext->isGranted('ROLE_COMPRAS_CREACION_SOLICITUD') //
                && false === $this->securityContext->isGranted('ROLE_COMPRAS_ENVIO_SOLICITUD')) {

            $usuario = $this->securityContext->getToken()->getUser();

            $idArea = $usuario->getArea()->getId();

            $builder->add('rubro', EntityType::clase, array(
                'class' => 'ADIF\ComprasBundle\Entity\Rubro',
                'required' => true,
                'label' => 'Rubro',
                'empty_value' => '-- Rubro --',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control choice '),
                'em' => $this->emCompras,
                'query_builder' => function(EntityRepository $er) use ( $idArea ) {

            return $er->createQueryBuilder('r')
                            ->where('r.idArea =  :idArea')
                            ->setParameter('idArea', $idArea)
                            ->orderBy('r.denominacionRubro', 'ASC');
        })
            );
        } // Sino        
        else {
            $builder->add('rubro', EntityType::clase, array(
                'class' => 'ADIF\ComprasBundle\Entity\Rubro',
                'required' => true,
                'label' => 'Rubro',
                'empty_value' => '-- Rubro --',
                'label_attr' => array('class' => 'control-label'),
                'em' => $this->emCompras,
                'attr' => array('class' => ' form-control choice '))
            );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\RenglonSolicitudCompra'
        ));
        
        //$resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_renglonsolicitudcompra';
    }

}
