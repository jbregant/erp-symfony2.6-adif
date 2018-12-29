<?php

namespace ADIF\ComprasBundle\EventListener;

use ADIF\ComprasBundle\Entity\AdicionalCotizacion;
use ADIF\ComprasBundle\Entity\BienEconomico;
use ADIF\ComprasBundle\Entity\Cliente;
use ADIF\ComprasBundle\Entity\ClienteProveedor;
use ADIF\ComprasBundle\Entity\DatosImpositivos;
use ADIF\ComprasBundle\Entity\EntidadAutorizante;
use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ComprasBundle\Entity\PedidoInterno;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\ComprasBundle\Entity\RenglonCotizacion;
use ADIF\ComprasBundle\Entity\RenglonOrdenCompra;
use ADIF\ComprasBundle\Entity\Rubro;
use ADIF\ComprasBundle\Entity\SolicitudCompra;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ADIF\ComprasBundle\Entity\Requerimiento;
use ADIF\ComprasBundle\Entity\HistoricoSolicitudCompra;

/**
 * PostLoadEventSubscriber
 *
 * @author Manuel Becerra
 * created 12/07/2014
 * 
 * 
 */
class PostLoadEventSubscriber {

    /**
     * CLASE_ADICIONAL_COTIZACION
     */
    const CLASE_ADICIONAL_COTIZACION = 'ADIF\ComprasBundle\Entity\AdicionalCotizacion';

    /**
     * CLASE_ADICIONAL
     */
    const CLASE_ADICIONAL = 'ADIF\ComprasBundle\Entity\Adicional';

    /**
     * CLASE_BIEN_ECONOMICO
     */
    const CLASE_BIEN_ECONOMICO = 'ADIF\ComprasBundle\Entity\BienEconomico';

    /**
     * CLASE_ENTIDAD_AUTORIZANTE
     */
    const CLASE_ENTIDAD_AUTORIZANTE = 'ADIF\ComprasBundle\Entity\EntidadAutorizante';

    /**
     * CLASE_CLIENTE_PROVEEDOR
     */
    const CLASE_CLIENTE_PROVEEDOR = 'ADIF\ComprasBundle\Entity\ClienteProveedor';

    /**
     * CLASE_DATOS_IMPOSITIVOS
     */
    const CLASE_DATOS_IMPOSITIVOS = 'ADIF\ComprasBundle\Entity\DatosImpositivos';

    /**
     * CLASE_CLIENTE
     */
    const CLASE_CLIENTE = 'ADIF\ComprasBundle\Entity\Cliente';

    /**
     * CLASE_PROVEEDOR
     */
    const CLASE_PROVEEDOR = 'ADIF\ComprasBundle\Entity\Proveedor';

    /**
     * CLASE_RUBRO
     */
    const CLASE_RUBRO = 'ADIF\ComprasBundle\Entity\Rubro';

    /**
     * CLASE_SOLICITUD_COMPRA
     */
    const CLASE_SOLICITUD_COMPRA = 'ADIF\ComprasBundle\Entity\SolicitudCompra';

    /**
     * CLASE_REQUERIMIENTO
     */
    const CLASE_REQUERIMIENTO = 'ADIF\ComprasBundle\Entity\Requerimiento';

    /**
     * CLASE_PEDIDO_INTERNO
     */
    const CLASE_PEDIDO_INTERNO = 'ADIF\ComprasBundle\Entity\PedidoInterno';

    /**
     * CLASE_RENGLON_COTIZACION
     */
    const CLASE_RENGLON_COTIZACION = 'ADIF\ComprasBundle\Entity\RenglonCotizacion';

    /**
     * CLASE_ORDEN_COMPRA
     */
    const CLASE_ORDEN_COMPRA = 'ADIF\ComprasBundle\Entity\OrdenCompra';

    /**
     * CLASE_RENGLON_ORDEN_COMPRA
     */
    const CLASE_RENGLON_ORDEN_COMPRA = 'ADIF\ComprasBundle\Entity\RenglonOrdenCompra';
    
    /**
     * CLASE_HISTORICO_SOLICITUD_COMPRA
     */
    const CLASE_HISTORICO_SOLICITUD_COMPRA = 'ADIF\ComprasBundle\Entity\HistoricoSolicitudCompra';


    /**
     *
     * @var type \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $registry;

    /**
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(Registry $registry) {

        $this->registry = $registry;
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs) {

        $this->updateEntities($eventArgs);
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs) {

        $this->updateEntities($eventArgs);
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs) {

        $entity = $eventArgs->getEntity();
        
         

        // Si la entidad es un Cliente
        if ($entity instanceof Cliente) {

            // CuentaContable
            if (null != $entity->getIdCuentaContable()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CLIENTE, //
                        'cuentaContable', //
                        'ADIF\ContableBundle\Entity\CuentaContable', //
                        $entity->getIdCuentaContable())
                ;
            }

            // TipoMoneda
            if (null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CLIENTE, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }

            $this->loadClienteProveedor($entity->getClienteProveedor(), $eventArgs);
        }

        // Si la entidad es un Proveedor
        if ($entity instanceof Proveedor) {

            // Nacionalidad
            if (null != $entity->getIdNacionalidad()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR, //
                        'nacionalidad', //
                        'ADIF\RecursosHumanosBundle\Entity\Nacionalidad', //
                        $entity->getIdNacionalidad())
                ;
            }

            // CuentaContable
            if (null != $entity->getIdCuentaContable()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR, //
                        'cuentaContable', //
                        'ADIF\ContableBundle\Entity\CuentaContable', //
                        $entity->getIdCuentaContable())
                ;
            }

            // TipoPago
            if (null != $entity->getIdTipoPago()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR, //
                        'tipoPago', //
                        'ADIF\ContableBundle\Entity\TipoPago', //
                        $entity->getIdTipoPago())
                ;
            }

            // TipoMoneda
            if (null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }

            // Cuenta
            if (null != $entity->getIdCuenta()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR, //
                        'cuenta', //
                        'ADIF\RecursosHumanosBundle\Entity\CuentaBancaria', //
                        $entity->getIdCuenta())
                ;
            }

            $this->loadClienteProveedor($entity->getClienteProveedor(), $eventArgs);
        }

        // Si la entidad es un Rubro
        if ($entity instanceof Rubro && null != $entity->getIdArea()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_RUBRO, //
                    'area', //
                    'ADIF\RecursosHumanosBundle\Entity\Area', //
                    $entity->getIdArea())
            ;
        }

        // Si la entidad es un BienEconomico
        if ($entity instanceof BienEconomico && null != $entity->getIdCuentaContable()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_BIEN_ECONOMICO, //
                    'cuentaContable', //
                    'ADIF\ContableBundle\Entity\CuentaContable', //
                    $entity->getIdCuentaContable())
            ;
        }

        // Si la entidad es una EntidadAutorizante
        if ($entity instanceof EntidadAutorizante && null != $entity->getIdGrupo()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ENTIDAD_AUTORIZANTE, //
                    'grupo', //
                    'ADIF\AutenticacionBundle\Entity\Grupo', //
                    $entity->getIdGrupo())
            ;
        }

        // Si la entidad es una SolicitudCompra
        if ($entity instanceof SolicitudCompra && null != $entity->getIdUsuario()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_SOLICITUD_COMPRA, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es una Requerimiento
        if ($entity instanceof Requerimiento && null != $entity->getIdUsuario()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_REQUERIMIENTO, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es una PedidoInterno
        if ($entity instanceof PedidoInterno) {

            if (null != $entity->getIdUsuario()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PEDIDO_INTERNO, //
                        'usuario', //
                        'ADIF\AutenticacionBundle\Entity\Usuario', //
                        $entity->getIdUsuario())
                ;
            }

            if (null != $entity->getIdArea()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PEDIDO_INTERNO, //
                        'area', //
                        'ADIF\RecursosHumanosBundle\Entity\Area', //
                        $entity->getIdArea())
                ;
            }

            if (null != $entity->getIdCentroCosto()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PEDIDO_INTERNO, //
                        'centroCosto', //
                        'ADIF\ContableBundle\Entity\CentroCosto', //
                        $entity->getIdCentroCosto())
                ;
            }
        }

        // Si la entidad es un RenglonCotizacion
        if ($entity instanceof RenglonCotizacion) {

            if (null != $entity->getIdAlicuotaIva()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_COTIZACION, //
                        'alicuotaIva', //
                        'ADIF\ContableBundle\Entity\AlicuotaIva', //
                        $entity->getIdAlicuotaIva())
                ;
            }

            if (null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_COTIZACION, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }
        }

        // Si la entidad es un OrdenCompra
        if ($entity instanceof OrdenCompra) {

            // Usuario
            if ($entity->getIdUsuario() != null) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_COMPRA, //
                        'usuario', //
                        'ADIF\AutenticacionBundle\Entity\Usuario', //
                        $entity->getIdUsuario())
                ;
            }

            // Domicilio Entrega
            if (null != $entity->getIdDomicilioEntrega()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_COMPRA, //
                        'domicilioEntrega', //
                        'ADIF\RecursosHumanosBundle\Entity\Domicilio', //
                        $entity->getIdDomicilioEntrega())
                ;
            }

            // TipoPago
            if (null != $entity->getIdTipoPago()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_COMPRA, //
                        'tipoPago', //
                        'ADIF\ContableBundle\Entity\TipoPago', //
                        $entity->getIdTipoPago())
                ;
            }

            // CondicionPago
            if (null != $entity->getIdCondicionPago()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_COMPRA, //
                        'condicionPago', //
                        'ADIF\ContableBundle\Entity\CondicionPago', //
                        $entity->getIdCondicionPago())
                ;
            }

            // TipoMoneda
            if (null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_COMPRA, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }
        }

        // Si la entidad es un RenglonOrdenCompra
        if ($entity instanceof RenglonOrdenCompra) {

            if (null != $entity->getIdAlicuotaIva()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_ORDEN_COMPRA, //
                        'alicuotaIva', //
                        'ADIF\ContableBundle\Entity\AlicuotaIva', //
                        $entity->getIdAlicuotaIva())
                ;
            }

            if (null != $entity->getIdCentroCosto()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_ORDEN_COMPRA, //
                        'centroCosto', //
                        'ADIF\ContableBundle\Entity\CentroCosto', //
                        $entity->getIdCentroCosto())
                ;
            }
        }

        // Si la entidad es un AdicionalCotizacion
        if ($entity instanceof AdicionalCotizacion) {

            if (null != $entity->getIdAlicuotaIva()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ADICIONAL_COTIZACION, //
                        'alicuotaIva', //
                        'ADIF\ContableBundle\Entity\AlicuotaIva', //
                        $entity->getIdAlicuotaIva())
                ;
            }
        }

        // Si la entidad es un Adicional
        if ($entity instanceof \ADIF\ComprasBundle\Entity\Adicional) {

            if (null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ADICIONAL, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }
        }
        
        // Si la entidad es una HistoricoSolicitudCompra
        if ($entity instanceof HistoricoSolicitudCompra && null != $entity->getIdUsuario()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_HISTORICO_SOLICITUD_COMPRA, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es un ClienteProveedor
        $this->loadClienteProveedor($entity, $eventArgs);


        // Si la entidad es un DatoImpositivo
        $this->loadDatosImpositivos($entity, $eventArgs);
    }

    /**
     * 
     * @param type $eventArgs
     * @param type $entityClass
     * @param type $property
     * @param type $referenceEntityClass
     * @param type $idEntity
     */
    private function setEntityValue($eventArgs, $entityClass, $property, $referenceEntityClass, $idEntity) {

        $em = $eventArgs->getEntityManager();

        $entity = $eventArgs->getEntity();
//\Doctrine\Common\Util\Debug::dump( $entity ); exit;
        $reflProp = $em->getClassMetadata($entityClass)
                ->reflClass->getProperty($property);

        $reflProp->setAccessible(true);

        $reflProp->setValue(
                $entity, $this->registry->getManagerForClass($referenceEntityClass)
                        ->getReference($referenceEntityClass, $idEntity)
        );
    }

    /**
     * 
     * @param type $eventArgs
     */
    private function updateEntities($eventArgs) {

        $entity = $eventArgs->getEntity();


        // Si la entidad es un Cliente o un Proveedor
        if (($entity instanceof Cliente || $entity instanceof Proveedor) && $entity->getClienteProveedor() != null) {

            // Domicilio Legal
            if (null != $entity->getClienteProveedor()->getDomicilioLegal()) {

                $entityId = $this->updateEntityId($entity->getClienteProveedor()->getDomicilioLegal());

                $entity->getClienteProveedor()->setIdDomicilioLegal($entityId);
            }

            // Domicilio Comercial
            if (null != $entity->getClienteProveedor()->getDomicilioComercial()) {

                $entityId = $this->updateEntityId($entity->getClienteProveedor()->getDomicilioComercial());

                $entity->getClienteProveedor()->setIdDomicilioComercial($entityId);
            }
        }

        // Si la entidad es una OrdenCompra
        if ($entity instanceof OrdenCompra) {

            // Domicilio Entrega
            if (null != $entity->getDomicilioEntrega()) {

                $entityId = $this->updateEntityId($entity->getDomicilioEntrega());

                $entity->setIdDomicilioEntrega($entityId);
            }
        }
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    private function updateEntityId($entity) {

        $entityManager = $this->registry->getManagerForClass(get_class($entity));

        $entityManager->persist($entity);
        $entityManager->flush();

        return $entity->getId();
    }

    /**
     * 
     * Carga de atributos de ClienteProveedor para los clientes y proveedores
     * 
     * @param type $entity
     * @param type $eventArgs
     */
    private function loadClienteProveedor($entity, $eventArgs) {

        if ($entity instanceof ClienteProveedor) {
            // DomicilioLegal
            if (null != $entity->getIdDomicilioLegal()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CLIENTE_PROVEEDOR, //
                        'domicilioLegal', //
                        'ADIF\RecursosHumanosBundle\Entity\Domicilio', //
                        $entity->getIdDomicilioLegal())
                ;
            }

            // DomicilioComercial
            if (null != $entity->getIdDomicilioComercial()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CLIENTE_PROVEEDOR, //
                        'domicilioComercial', //
                        'ADIF\RecursosHumanosBundle\Entity\Domicilio', //
                        $entity->getIdDomicilioComercial())
                ;
            }

            $this->loadDatosImpositivos($entity->getDatosImpositivos(), $eventArgs);
        }
    }

    /**
     * 
     * Carga los atributos de DatosImpositivos
     * 
     * @param type $entity
     * @param type $eventArgs
     */
    private function loadDatosImpositivos($entity, $eventArgs) {

        if ($entity instanceof DatosImpositivos) {

            // Condicion IVA
            if (null != $entity->getIdCondicionIVA()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_DATOS_IMPOSITIVOS, //
                        'condicionIVA', //
                        'ADIF\ContableBundle\Entity\TipoResponsable', //
                        $entity->getIdCondicionIVA())
                ;
            }

            // Condicion Ganancias
            if (null != $entity->getIdCondicionGanancias()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_DATOS_IMPOSITIVOS, //
                        'condicionGanancias', //
                        'ADIF\ContableBundle\Entity\TipoResponsable', //
                        $entity->getIdCondicionGanancias())
                ;
            }

            // Condicion Ingresos Brutos
            if (null != $entity->getIdCondicionIngresosBrutos()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_DATOS_IMPOSITIVOS, //
                        'condicionIngresosBrutos', //
                        'ADIF\ContableBundle\Entity\TipoResponsable', //
                        $entity->getIdCondicionIngresosBrutos())
                ;
            }

            // Condicion SUSS
            if (null != $entity->getIdCondicionSUSS()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_DATOS_IMPOSITIVOS, //
                        'condicionSUSS', //
                        'ADIF\ContableBundle\Entity\TipoResponsable', //
                        $entity->getIdCondicionSUSS())
                ;
            }
        }
    }

}
