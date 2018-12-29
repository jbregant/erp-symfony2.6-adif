<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRendicionEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteJurisdiccion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Form\ComprobanteCompraCreateType;
use ADIF\ContableBundle\Form\ComprobanteCompraType;
use DateTime;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoMoneda;
use Doctrine\DBAL\Types\Type;

/**
 * ComprobanteCompra controller.
 *
 * @Route("/comprobantescompra")
 */
class ComprobanteCompraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;
    private $emContable;
    private $emCompras;
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Comprobantes de compra' => $this->generateUrl('comprobantes_compra')
        );
    }

    /**
     * Lists all ComprobanteCompra entities.
     *
     * @Route("/", name="comprobantes_compra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de compra'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Comprobantes de compra',
            'page_info' => 'Lista de comprobantes de compra'
        );
    }

    /**
     * Creates a new ComprobanteCompra entity.
     *
     * @Route("/insertar", name="comprobantes_compra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ComprobanteCompra:new.html.twig")
     */
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        
        $this->emContable = $em;
        $this->emCompras = $em_compras;
        
        $diferenciaTipoCambio = 0;
		
		$mensaje = '';
		
		$restante = 0;

        $requestComprobanteCompra = $request->request->get('adif_contablebundle_comprobantecompra');
        
        $ordenCompra = null;
        
        $requestRenglonComprobanteCompra = array_values($requestComprobanteCompra["renglonesComprobante"]);
        
        $tipoComprobante = $request->request->get('adif_contablebundle_comprobantecompra', false)['tipoComprobante'];
		
		$tipoCambio = $request->get('strTipoCambio', null);
		$tipoCambio = str_replace(',', '.', $tipoCambio);
		
		$esOcMonedaExtranjera = $request->get('esOcMonedaExtranjera');
        $esOcMonedaExtranjera = ($esOcMonedaExtranjera == 1) ? true : false;
		
        $comprobanteCompra = ConstanteTipoComprobanteCompra::getSubclass($tipoComprobante);
        
		$tipoMoneda = $em->getRepository('ADIFContableBundle:TipoMoneda')->findOneBy(array(
			'codigoTipoMoneda' => ConstanteTipoMoneda::PESO_ARGENTINO
		));
		
		$comprobanteCompra->setTipoMoneda($tipoMoneda);
		
		if (empty($tipoCambio) || $tipoCambio == null) {
			$tipoCambio = 1;
		}
        
		$comprobanteCompra->setTipoCambio($tipoCambio);

        $esNotaCredito = $comprobanteCompra->getEsNotaCredito();
        
        $esContraAsiento = false;
        if ($esNotaCredito) {
            $esContraAsiento = true;
        } 

        $form = $this->createCreateForm($comprobanteCompra);
        $form->handleRequest($request);
        
        if ($form->isValid()) {

            $comprobanteCompra->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

			$mensajeErrorSetComprobanteImpresion = $this->setComprobanteImpresion($comprobanteCompra);
				
			if ($mensajeErrorSetComprobanteImpresion != '') {
				$this->get('session')->getFlashBag()->add('error', $mensajeErrorSetComprobanteImpresion);
				$request->attributes->set('form-error', true);
				return $this->redirect($this->generateUrl('comprobantes_compra_new'));		
			}
			
            foreach ($comprobanteCompra->getRenglonesComprobante() as $index => $renglonComprobanteCompra) {

                /* @var $renglonComprobanteCompra \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */

                $renglonOrdenCompra = $em_compras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                        ->find($renglonComprobanteCompra->getIdRenglonOrdenCompra());
						
				$ordenCompra = $renglonOrdenCompra->getOrdenCompra();
                
                // Si el comprobante NO es una NotaCredito
                if (!$esNotaCredito) {
                    // Si es FC, ND, Recibo, Ticket factura va a restar siempre - @gluis - 19/03/2018
                    $renglonOrdenCompra->setRestante($renglonOrdenCompra->getRestante() - $renglonComprobanteCompra->getCantidad());

                } else {
                    // Sino, si es una NotaCredito
                    // Si es una Devolucion
                    if ($renglonComprobanteCompra->getEsDevolucion()) {

                        $renglonOrdenCompra->setRestante($renglonOrdenCompra->getRestante() + $renglonComprobanteCompra->getCantidad());
                    }
                }
				
                $renglonComprobanteCompra->setBienEconomico($renglonOrdenCompra->getBienEconomico());
				
				$restante += $renglonOrdenCompra->getRestante();
            }
			

            $totalComprobante = $comprobanteCompra->getTotal() - $comprobanteCompra->getImporteTotalPercepcion() - $comprobanteCompra->getImporteTotalImpuesto();
            $totalOC = ($ordenCompra->getSaldoMonedaExtranjera() != null && $ordenCompra->getSaldoMonedaExtranjera() != 0 ) 
            ? $ordenCompra->getSaldoMonedaExtranjera() 
            : ( $ordenCompra->getMonto() != null ? $ordenCompra->getMonto() : 0 );
                    $totalReal = $totalOC * $tipoCambio;
			
            if ($esOcMonedaExtranjera) {
                /**
                * Se establece una tolerancia por diferencia de cambio del 1%
                * $toleranciaPermitida = 1
                * @gluis - 08/11/2017
                */
				
				//echo 'OC ID ' . $ordenCompra->getId() . ' OC nro: ' . $ordenCompra->getNumeroOrdenCompra() . ' fecha ' . $ordenCompra->getFechaOrdenCompra()->format('d/m/Y');
                //exit;
				
                $totalComprobanteMonedaExtranjera = $totalComprobante / $tipoCambio;
                
                $saldoMonedaExtranjeraOC = 0;
                 
                // Diferencia del total REAL vs SIGA
                $diferenciaTipoCambio = $totalReal - $totalComprobante;
                
                $epsilon = 0.01;
                if(abs($diferenciaTipoCambio) <= $epsilon) {
                    $diferenciaTipoCambio = 0;
                }
                
                $toleranciaPermitida = 1; 
				if ($totalReal != 0) {
					// Evito el error "division by cero"
					$porcentajeDiferenciaCambio = abs($diferenciaTipoCambio * 100 / $totalReal);
				} else {
					$mensaje = 'El total real de la OC no puede ser cero.';
					$this->get('session')->getFlashBag()->add('error', $mensaje);
					$request->attributes->set('form-error', true);
					return $this->redirect($this->generateUrl('comprobantes_compra_new'));		
				}
                
/*                
                echo 'OC ID ' . $ordenCompra->getId() . ' OC nro: ' . $ordenCompra->getNumeroOrdenCompra() . ' fecha ' . $ordenCompra->getFechaOrdenCompra()->format('d/m/Y');
                echo "<br>----------------<br>";
                echo "Tipo de cambio = $tipoCambio";
                echo "<br>----------------<br>";
                echo "Total real (OC) = $totalReal";
                echo "<br>----------------<br>";
                echo "Total comprobante ingresado = $totalComprobante";
                echo "<br>----------------<br>";
                echo "Diferencia tipo de cambio = $diferenciaTipoCambio";
                echo "<br>----------------<br>";
                echo "Porcentaje por diferencia de cambio = $porcentajeDiferenciaCambio";
				echo "<br>----------------<br>";
				echo "Cantidad restante OC = $restante";
                exit;
*/				
//                var_dump($totalOC, $totalComprobanteMonedaExtranjera, $porcentajeDiferenciaCambio); exit;
                if ($porcentajeDiferenciaCambio > $toleranciaPermitida && $totalComprobanteMonedaExtranjera > $totalOC) {

                    $mensaje = 'La diferencia de totales entre el comprobante ingresado y la OC, ';
                    $mensaje .= 'es m&aacute;s grande que la tolerancia permitida por el sistema (1% del total OC).';
                    $mensaje .= 'El saldo en moneda extranjera pendiente es: ';
                    $mensaje .= $ordenCompra->getTipoMoneda()->getCodigoTipoMoneda() . ' ' . number_format($ordenCompra->getSaldoMonedaExtranjera(), 2, ',', '.') . ' ';
                    $mensaje .= 'y el total del comprobante ingresado es: ' . $ordenCompra->getTipoMoneda()->getCodigoTipoMoneda() . ' ' . number_format($totalComprobanteMonedaExtranjera, 2, ',', '.');

                    $this->get('session')->getFlashBag()->add('error', $mensaje);

                    $request->attributes->set('form-error', true);
                    return $this->redirect($this->generateUrl('comprobantes_compra_new'));	

                } else {
                    
                    if ($restante == 0) {
                        
                        $saldoMonedaExtranjeraOC = 0;
                        
                    } else {
                        
                        if ($esNotaCredito) {
                            $saldoMonedaExtranjeraOC = $ordenCompra->getSaldoMonedaExtranjera() + $totalComprobanteMonedaExtranjera;
                        } else {
                            $saldoMonedaExtranjeraOC = $ordenCompra->getSaldoMonedaExtranjera() - $totalComprobanteMonedaExtranjera;
                        }
                    }
                }
                
                $ordenCompra->setSaldoMonedaExtranjera($saldoMonedaExtranjeraOC);
                $em_compras->persist($ordenCompra);
                
                $comprobanteCompra->setTotalOcMonedaExtranjera($totalOC);
				$comprobanteCompra->setTotalMonedaExtranjera($totalComprobanteMonedaExtranjera);
            }
			
            // Seteo el saldo
            $comprobanteCompra->setSaldo($comprobanteCompra->getTotal());
			
            // Persisto la entidad
            $em->persist($comprobanteCompra);

            // Persisto los asientos contables y presupuestarios
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoCompras($comprobanteCompra, $this->getUser(), $esContraAsiento);

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
					// Fix: se cambio de orden los flush, para que primero inserte/updetee la OC y sus renglones
					// y luego la parte del comprobante, ya que se agrego la FK en renglon_comprobante "FK_renglon_orden_compra",
					// Este fix previene el error: 
					// General error: 1205 Lock wait timeout exceeded; try restarting transaction
					
					$em_compras->flush();
					
                    $em->flush();

                    $em->getConnection()->commit();

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteCompra->getId(),
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('comprobantes_compra'));
        } else {
            $comprobanteCompra->setProveedor($em_compras->getRepository('ADIFComprasBundle:Proveedor')
                            ->find($comprobanteCompra->getIdProveedor()));

            $request->attributes->set('form-error', true);
        }

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteCompra,
            'form' => $form->createView(),
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de compra',
        );
    }

    /**
     * Creates a form to create a ComprobanteCompra entity.
     *
     * @param ComprobanteCompra $entity The entity
     *
     * 
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ComprobanteCompra $entity) {
        $form = $this->createForm(new ComprobanteCompraCreateType(  $this->getDoctrine()->getManager($this->getEntityManager()),
                                                                    $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
                ), $entity, array(
            'action' => $this->generateUrl('comprobantes_compra_create'),
            'method' => 'POST',
           
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ComprobanteCompra entity.
     *
     * @Route("/crear", name="comprobantes_compra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $comprobanteCompra = new ComprobanteCompra();

        $form = $this->createCreateForm($comprobanteCompra);

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteCompra,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de compra'
        );
    }

    /**
     * Finds and displays a ComprobanteCompra entity.
     *
     * @Route("/{id}", name="comprobantes_compra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['ComprobanteCompra'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante de compra'
        );
    }

    /**
     * Displays a form to edit an existing ComprobanteCompra entity.
     *
     * @Route("/editar/{id}", name="comprobantes_compra_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ComprobanteCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);
        
        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $this->emContable = $em;
        $this->emCompras = $em_compras;
        
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de compra'
        );
    }

    /**
     * Creates a form to edit a ComprobanteCompra entity.
     *
     * @param ComprobanteCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    
    private function createEditForm(ComprobanteCompra $entity) {
        $form = $this->createForm(new ComprobanteCompraType( $this->getDoctrine()->getManager($this->getEntityManager()),
                                                             $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
                ), $entity, array(
            'action' => $this->generateUrl('comprobantes_compra_update', array('id' => $entity->getId())),
            'method' => 'PUT',

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ComprobanteCompra entity.
     *
     * @Route("/actualizar/{id}", name="comprobantes_compra_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ComprobanteCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprobantes_compra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de compra'
        );
    }

    /**
     * Deletes a ComprobanteCompra entity.
     *
     * @Route("/borrar/{id}", name="comprobantes_compra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('comprobantes_compra'));
    }

    /**
     * Muestra la pantalla de libro IVA compras.
     *
     * @Route("/libroiva_compras/", name="comprobantes_compra_libroiva_compras")
     * @Method("GET")
     * @Template("ADIFContableBundle:ComprobanteCompra:reporte.libro_iva_compras.html.twig")
     */
    public function libroIVAComprasAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Libro IVA compras'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Libro IVA compras'
        );
    }

    /**
     * @Route("/filtrar_libroiva_compras/", name="comprobantes_compra_filtrar_libroiva_compras")
     */
    public function filtrarLibroIVAComprasAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaInicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $letraY = ConstanteLetraComprobante::Y;

        $neto2_5 = 0;
        $neto105 = 0;
        $neto21 = 0;
        $neto27 = 0;
        $totalNeto = 0;
        $iva2_5 = 0;
        $iva105 = 0;
        $iva21 = 0;
        $iva27 = 0;
        $totalIva = 0;
        $fechaCreacion = null;
        $fechaAnulacion = null;

        /** COMPROBANTES DE COMPRA **/
        $qb = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:ComprobanteCompra', $this->getEntityManager())
                ->createQueryBuilder('c');
         
        $comprobantes_compra = $qb
                ->leftJoin('c.letraComprobante', 'l')
                ->leftJoin('c.estadoComprobante', 'e')
                ->where($qb->expr()->between('c.fechaCreacion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
//                ->andWhere('e.id != :estadoAnulado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
//                ->setParameter('estadoAnulado', EstadoComprobante::__ESTADO_ANULADO)
                ->addOrderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        // ANULADAS
        $qb = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:ComprobanteCompra', $this->getEntityManager())
                ->createQueryBuilder('c');
         
        $comprobantes_compraA = $qb
                ->leftJoin('c.letraComprobante', 'l')
                ->leftJoin('c.estadoComprobante', 'e')
                ->where($qb->expr()->between('c.fechaAnulacion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
                ->andWhere('e.id = :estadoAnulado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
                ->setParameter('estadoAnulado', EstadoComprobante::__ESTADO_ANULADO)
                ->addOrderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        $comprobantes_compra = array_merge($comprobantes_compra, $comprobantes_compraA);
        asort($comprobantes_compra);
        /** FIN COMPROBANTES DE COMPRA **/
        
        /** COMPROBANTES DE CONSULTORIA **/
        $qbc = $this->getDoctrine()->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria', $this->getEntityManager())
                ->createQueryBuilder('cc');

        $comprobantes_consultoria = $qbc
                ->leftJoin('cc.letraComprobante', 'l')
                ->leftJoin('cc.estadoComprobante', 'e')
                ->where($qbc->expr()->between('cc.fechaCreacion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
//                ->andWhere('e.id != :estadoAnulado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
//                ->setParameter('estadoAnulado', EstadoComprobante::__ESTADO_ANULADO)
                ->addOrderBy('cc.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        // ANULADAS
        $qbc = $this->getDoctrine()->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria', $this->getEntityManager())
                ->createQueryBuilder('cc');

        $comprobantes_consultoriaA = $qbc
                ->leftJoin('cc.letraComprobante', 'l')
                ->leftJoin('cc.estadoComprobante', 'e')
                ->where($qbc->expr()->between('cc.fechaAnulacion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
                ->andWhere('e.id = :estadoAnulado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
                ->setParameter('estadoAnulado', EstadoComprobante::__ESTADO_ANULADO)
                ->addOrderBy('cc.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        $comprobantes_consultoria = array_merge($comprobantes_consultoria, $comprobantes_consultoriaA);
        asort($comprobantes_consultoria);
        /** FIN COMPROBANTES DE CONSULTORIA **/

        /** COMPROBANTES OBRA **/
        $qbo = $this->getDoctrine()->getRepository('ADIFContableBundle:Obras\ComprobanteObra', $this->getEntityManager())
                ->createQueryBuilder('co');

        $comprobantes_obra = $qbo
                ->leftJoin('co.letraComprobante', 'l')
                ->leftJoin('co.estadoComprobante', 'e')
                ->where($qbo->expr()->between('co.fechaCreacion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
//                ->andWhere('e.id != :estadoAnulado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
//                ->setParameter('estadoAnulado', EstadoComprobante::__ESTADO_ANULADO)
                ->addOrderBy('co.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        $qbo = $this->getDoctrine()->getRepository('ADIFContableBundle:Obras\ComprobanteObra', $this->getEntityManager())
                ->createQueryBuilder('co');
        
        // LOS ANULADOS 
        $comprobantes_obraA = $qbo
                ->leftJoin('co.letraComprobante', 'l')
                ->leftJoin('co.estadoComprobante', 'e')
                ->where($qbo->expr()->between('co.fechaAnulacion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
                ->andWhere('e.id = :estadoAnulado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
                ->setParameter('estadoAnulado', EstadoComprobante::__ESTADO_ANULADO)
                ->addOrderBy('co.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        $comprobantes_obra = array_merge($comprobantes_obra, $comprobantes_obraA);
        asort($comprobantes_obra);
        /** FIN COMPROBANTES OBRA **/
        
        /** COMPROBANTES DE EGRESO DE VALOR ***/
        $qbr = $this->getDoctrine()->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor', $this->getEntityManager())
                ->createQueryBuilder('cev');

        $comprobantes_egreso_valor = $qbr
                ->leftJoin('cev.letraComprobante', 'l')
                ->innerJoin('cev.rendicionEgresoValor', 'r')
                ->innerJoin('r.estadoRendicionEgresoValor', 'e')
                ->where($qbr->expr()->between('r.fechaRendicion', ':fechaInicio', ':fechaFin'))
                ->andWhere('l.letra != :letraY')
                ->andWhere('e.codigo = :estadoCerrado')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('letraY', $letraY)
                ->setParameter('estadoCerrado', ConstanteEstadoRendicionEgresoValor::ESTADO_GENERADA)
                ->addOrderBy('cev.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();

        /** FIN COMPROBANTES DE EGRESO DE VALOR ***/
        
        $jsonResult = [];

        $comprobantes = array_merge($comprobantes_compra, $comprobantes_consultoria, $comprobantes_obra, $comprobantes_egreso_valor);

//        $arrIds = array();
//        foreach ($comprobantes as $comprobante) {
//            
//            $arrIds[] = $comprobante->getId();
//            if ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) {
//                $arrIds[] = $comprobante->getId() * -1;
//            }
//        } 
        
        //var_dump($arrIds);
        //exit;
        $id = $idAnt = null;
        foreach ($comprobantes as $comprobante) {
            
            $id = $comprobante->getId();
        
            if ($id != $idAnt) {
            
                if ((!method_exists($comprobante, 'getOrdenPago')) || (method_exists($comprobante, 'getOrdenPago') && ($comprobante->getOrdenPago() == null || ($comprobante->getOrdenPago() != null && !$comprobante->getOrdenPago()->getEstaAnulada())))) {

                    $fechaCreacion = $comprobante->getFechaCreacion();

                    if ($fechaCreacion >= $fechaInicio && $fechaCreacion <= $fechaFin) {

                        $multiplicador = $comprobante->getEsNotaCredito() ? -1 : 1;

                        $neto2_5 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_2_5);
                        $neto105 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_10_5);
                        $neto21 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_21);
                        $neto27 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_27);
                        $totalNeto = $neto105 + $neto21 + $neto27 + $neto2_5;

                        $iva2_5 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_2_5);
                        $iva105 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_10_5);
                        $iva21 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_21);
                        $iva27 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_27);
                        $totalIva = $iva105 + $iva21 + $iva27 + $iva2_5;

                        $arr_comp = array(
                            'id' => $comprobante->getId(),
                            'idAsiento' => ( $comprobante->getAsientoContable() != null ? $comprobante->getAsientoContable()->getId() : "No hay"),
                            'fechaComprobante' => $comprobante->getFechaComprobante()->format('d/m/Y'),
                            'numeroComprobante' => $comprobante->getNumeroCompleto(),
                            'tipoComprobante' => $comprobante->getTipoComprobante()->__toString(),
                            'letraComprobante' => $comprobante->getLetraComprobante() ? $comprobante->getLetraComprobante()->__toString() : '-',
                            'razonSocial' => $comprobante->getBeneficiarioIVACompras()->getRazonSocial(),
                            'cuit' => $comprobante->getBeneficiarioIVACompras()->getCUIT(),
                            'condicionImpositiva' => $comprobante->getCondicionImpositivaIVACompras(),
                            'importeNeto2_5' => number_format($neto2_5 * $multiplicador, 2, ',', '.'),
                            'importeNeto105' => number_format($neto105 * $multiplicador, 2, ',', '.'),
                            'importeNeto21' => number_format($neto21 * $multiplicador, 2, ',', '.'),
                            'importeNeto27' => number_format($neto27 * $multiplicador, 2, ',', '.'),
                            'importeTotalNeto' => number_format($totalNeto * $multiplicador, 2, ',', '.'),
                            'importeTotalExento' => number_format($comprobante->getImporteTotalExento() * $multiplicador, 2, ',', '.'),
                            'iva2_5' => number_format($iva2_5 * $multiplicador, 2, ',', '.'),
                            'iva105' => number_format($iva105 * $multiplicador, 2, ',', '.'),
                            'iva21' => number_format($iva21 * $multiplicador, 2, ',', '.'),
                            'iva27' => number_format($iva27 * $multiplicador, 2, ',', '.'),
                            'totalIVA' => number_format($totalIva * $multiplicador, 2, ',', '.'),
                            'percepciones' => ['IIBB' => []],
                            'total_percepciones' => [
                                'IIBB' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB) * $multiplicador, 2, ',', '.'),
                                'GCIAS' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_GANANCIAS) * $multiplicador, 2, ',', '.'),
                                'IVA' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA) * $multiplicador, 2, ',', '.'),
                                'SUSS' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_SUSS) * $multiplicador, 2, ',', '.'),
                            ],
                            'totalOtrosImpuestos' => number_format($comprobante->getImporteTotalImpuestoByConcepto(ConstanteConceptoImpuesto::CONCEPTO_OTROS_IMPUESTOS) * $multiplicador, 2, ',', '.'),
                            'totalImpuestosInternos' => number_format($comprobante->getImporteTotalImpuestoByConcepto(ConstanteConceptoImpuesto::CONCEPTO_IMPUESTOS_INTERNOS) * $multiplicador, 2, ',', '.'),
                            'totalFactura' => number_format($comprobante->getTotal() * $multiplicador, 2, ',', '.'),
                        );

                        // SOLO IIBB
                        foreach ($comprobante->getRenglonesPercepcion() as $renglonPercepcion) {

                            if ($renglonPercepcion->getConceptoPercepcion() == ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB) {

                                if (null != $renglonPercepcion->getJurisdiccion()) {
                                    $arr_comp['percepciones']['IIBB'][] = [
                                        'jurisdiccion' => $renglonPercepcion->getJurisdiccion()->getDenominacion(),
                                        'monto' => number_format($renglonPercepcion->getMonto() * $multiplicador, 2, ',', '.')
                                    ];
                                }
                            }
                        }

                        $jsonResult[] = $arr_comp;
                    }

                    if ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) {

                        $multiplicador = $comprobante->getEsNotaCredito() ? 1 : -1;

                        $fechaAnulacion = $comprobante->getFechaAnulacion();
                       
                        if ($fechaAnulacion >= $fechaInicio && $fechaAnulacion <= $fechaFin) {
                            $neto2_5 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_2_5);
                            $neto105 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_10_5);
                            $neto21 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_21);
                            $neto27 = $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_27);
                            $totalNeto = $neto105 + $neto21 + $neto27 + $neto2_5;

                            $iva2_5 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_2_5);
                            $iva105 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_10_5);
                            $iva21 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_21);
                            $iva27 = $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_27);
                            $totalIva = $iva105 + $iva21 + $iva27 + $iva2_5;
                            
                            $arr_comp = array(
                                'id' => $comprobante->getId(),
                                'idAsiento' => ( $comprobante->getAsientoContable() != null ? $comprobante->getAsientoContable()->getId() : "No hay"),
                                'fechaComprobante' => $comprobante->getFechaComprobante()->format('d/m/Y'),
                                'numeroComprobante' => $comprobante->getNumeroCompleto(),
                                'tipoComprobante' => $comprobante->getTipoComprobante()->__toString(),
                                'letraComprobante' => $comprobante->getLetraComprobante()->__toString(),
                                'razonSocial' => $comprobante->getBeneficiarioIVACompras()->getRazonSocial(),
                                'cuit' => $comprobante->getBeneficiarioIVACompras()->getCUIT(),
                                'condicionImpositiva' => $comprobante->getCondicionImpositivaIVACompras(),
                                'importeNeto2_5' => number_format($neto2_5 * $multiplicador, 2, ',', '.'),
                                'importeNeto105' => number_format($neto105 * $multiplicador, 2, ',', '.'),
                                'importeNeto21' => number_format($neto21 * $multiplicador, 2, ',', '.'),
                                'importeNeto27' => number_format($neto27 * $multiplicador, 2, ',', '.'),
                                'importeTotalNeto' => number_format($totalNeto * $multiplicador, 2, ',', '.'),
                                'importeTotalExento' => number_format($comprobante->getImporteTotalExento() * $multiplicador, 2, ',', '.'),
                                'iva2_5' => number_format($iva2_5 * $multiplicador, 2, ',', '.'),
                                'iva105' => number_format($iva105 * $multiplicador, 2, ',', '.'),
                                'iva21' => number_format($iva21 * $multiplicador, 2, ',', '.'),
                                'iva27' => number_format($iva27 * $multiplicador, 2, ',', '.'),
                                'totalIVA' => number_format($totalIva * $multiplicador, 2, ',', '.'),
                                'percepciones' => ['IIBB' => []],
                                'total_percepciones' => [
                                    'IIBB' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB) * $multiplicador, 2, ',', '.'),
                                    'GCIAS' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_GANANCIAS) * $multiplicador, 2, ',', '.'),
                                    'IVA' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA) * $multiplicador, 2, ',', '.'),
                                    'SUSS' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_SUSS) * $multiplicador, 2, ',', '.'),
                                ],
                                'totalOtrosImpuestos' => number_format($comprobante->getImporteTotalImpuestoByConcepto(ConstanteConceptoImpuesto::CONCEPTO_OTROS_IMPUESTOS) * $multiplicador, 2, ',', '.'),
                                'totalImpuestosInternos' => number_format($comprobante->getImporteTotalImpuestoByConcepto(ConstanteConceptoImpuesto::CONCEPTO_IMPUESTOS_INTERNOS) * $multiplicador, 2, ',', '.'),
                                'totalFactura' => number_format($comprobante->getTotal() * $multiplicador, 2, ',', '.'),
                            );

                            // SOLO IIBB
                            foreach ($comprobante->getRenglonesPercepcion() as $renglonPercepcion) {

                                if ($renglonPercepcion->getConceptoPercepcion() == ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB) {

                                    if (null != $renglonPercepcion->getJurisdiccion()) {
                                        $arr_comp['percepciones']['IIBB'][] = [
                                            'jurisdiccion' => $renglonPercepcion->getJurisdiccion()->getDenominacion(),
                                            'monto' => number_format($renglonPercepcion->getMonto() * $multiplicador, 2, ',', '.')
                                        ];
                                    }
                                }
                            }

                            $jsonResult[] = $arr_comp;
                        }
                    }
                }
            }
            
            $idAnt = $comprobante->getId();
        }

        $renglonesGastosBancarios = $emContable->getRepository('ADIFContableBundle:RenglonIvaCompras')
                ->findAllBetweenAnd($fechaInicio, $fechaFin);

        foreach ($renglonesGastosBancarios as $renglonGastoBancario) {
            /* @var $renglonGastoBancario \ADIF\ContableBundle\Entity\RenglonIvaCompras */

            $arr_comp = array(
                'id' => $renglonGastoBancario->getId(),
                'idAsiento' => "No hay",
                'fechaComprobante' => $renglonGastoBancario->getFecha()->format('d/m/Y'),
                'numeroComprobante' => $renglonGastoBancario->getConciliacion()->getId(),
                'tipoComprobante' => 'RESUMEN BANCARIO',
                'letraComprobante' => '',
                'razonSocial' => $renglonGastoBancario->getConciliacion()->getCuenta()->getIdBanco()->getNombre(),
                'cuit' => $renglonGastoBancario->getConciliacion()->getCuenta()->getIdBanco()->getNombre(),
                'condicionImpositiva' => ConstanteTipoResponsable::INSCRIPTO,
                'importeNeto2_5' => number_format(0, 2, ',', '.'),
                'importeNeto105' => number_format(0, 2, ',', '.'),
                'importeNeto21' => number_format($renglonGastoBancario->getNeto21(), 2, ',', '.'),
                'importeNeto27' => number_format(0, 2, ',', '.'),
                'importeTotalNeto' => number_format($renglonGastoBancario->getNeto21(), 2, ',', '.'),
                'importeTotalExento' => number_format($renglonGastoBancario->getGastosExentos(), 2, ',', '.'),
                'iva2_5' => number_format(0, 2, ',', '.'),
                'iva105' => number_format(0, 2, ',', '.'),
                'iva21' => number_format($renglonGastoBancario->getIva21(), 2, ',', '.'),
                'iva27' => number_format(0, 2, ',', '.'),
                'totalIVA' => number_format($renglonGastoBancario->getIva21(), 2, ',', '.'),
                'percepciones' => ['IIBB' => []],
                'total_percepciones' => [
                    'IIBB' => number_format($renglonGastoBancario->getIIBB901() + $renglonGastoBancario->getIIBB902(), 2, ',', '.'),
                    'GCIAS' => number_format(0, 2, ',', '.'),
                    'IVA' => number_format(0, 2, ',', '.'),
                    'SUSS' => number_format(0, 2, ',', '.'),
                ],
                'totalOtrosImpuestos' => number_format($renglonGastoBancario->getOtrosImpuestos(), 2, ',', '.'),
                'totalImpuestosInternos' => number_format(0, 2, ',', '.'),
                'totalFactura' => number_format($renglonGastoBancario->getTotal(), 2, ',', '.'),
            );

            $arr_comp['percepciones']['IIBB'][] = [
                'jurisdiccion' => 'CABA',
                'monto' => number_format($renglonGastoBancario->getIIBB901(), 2, ',', '.')
            ];
            /*
            $arr_comp['percepciones']['IIBB'][] = [
                'jurisdiccion' => 'Provincia Bs As',
                'monto' => number_format($renglonGastoBancario->getIIBB902(), 2, ',', '.')
            ];
             * 
             */


            $jsonResult[] = $arr_comp;
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * Tabla para ComprobanteCompra.
     *
     * @Route("/index_table/", name="comprobantescompra_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fechaComprobante', 'fechaComprobante');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('letraComprobante', 'letraComprobante');
        $rsm->addScalarResult('puntoVenta', 'puntoVenta');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('numeroOrdenCompra', 'numeroOrdenCompra');
        $rsm->addScalarResult('idProveedor', 'idProveedor');
        $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
        $rsm->addScalarResult('importePendientePago', 'importePendientePago');
        $rsm->addScalarResult('total', 'total');

        $native_query = $em->createNativeQuery('
            SELECT
                id,
                fechaComprobante,
                tipoComprobante,
                letraComprobante,
                puntoVenta,
                numero,
                cuit,
                razonSocial,
                numeroOrdenCompra,
                idProveedor,
                idEstadoComprobante,
                importePendientePago,
                total
            FROM
                vistacomprobantescompra           
        ', $rsm);

        $comprobantes = $native_query->getResult();

        return $this->render('ADIFContableBundle:ComprobanteCompra:index_table.html.twig', array('entities' => $comprobantes));
    }

    /**
     *
     * @Route("/index_table_comprobante_credito/", name="comprobantecompra_index_table_comprobante_credito")
     * @Method("GET|POST")
     */
    public function indexTableComprobantesCreditoAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fecha', 'fecha');
        $rsm->addScalarResult('tipo', 'tipo');
        $rsm->addScalarResult('idTipo', 'idTipo');
        $rsm->addScalarResult('cuitAndRazonSocial', 'cuitAndRazonSocial');
        $rsm->addScalarResult('numeroOrdenCompra', 'numeroOrdenCompra');
        $rsm->addScalarResult('idProveedor', 'idProveedor');
        $rsm->addScalarResult('monto', 'monto');
        $rsm->addScalarResult('estadoComprobante', 'estadoComprobante');
        $rsm->addScalarResult('esAnticipo', 'esAnticipo');
        $rsm->addScalarResult('yaUtilizada', 'yaUtilizada');

        $native_query = $em->createNativeQuery('
            SELECT
                id,
                fecha,
                tipo,
                idTipo,
                cuitAndRazonSocial,
                numeroOrdenCompra,
                idProveedor,
                monto,
                estadoComprobante,
                esAnticipo,
                yaUtilizada
            FROM
                vistacomprobantescompracredito           
        ', $rsm);

        $comprobantesCreditoArray = $native_query->getResult();

        return $this->render('ADIFContableBundle:ComprobanteCompra:index_table_comprobante_credito.html.twig', array(
                    'entities' => $comprobantesCreditoArray,
                        )
        );
    }

    /**
     * Anula el comprobante
     *
     * @Route("/anular/{id}", name="comprobantecompra_anular")
     * @Method("GET")
     */
    public function anularComprobanteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        /* @var $entity ComprobanteCompra */
        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);
		
		$esOcMonedaExtranjera = ($entity->getTotalOcMonedaExtranjera() != null);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);

        /////////
        //COMIENZO RUTINA QUE VERIFICA SI SE PUEDE ELIMINAR UN COMPROBANTE (chequeando las cantidades)
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id_renglon_orden_compra', 'id_renglon_orden_compra');
        $rsm->addScalarResult('cantidad_restante', 'cantidad_restante');
        $rsm->addScalarResult('cantidad_renglon_oc', 'cantidad_renglon_oc');
        
        $native_query = $em->createNativeQuery('CALL sp_verificacion_anulacion_nc(?,?) ', $rsm);

        $NroOrdenCompra = $entity->getOrdenCompra();
        $IdCbte = $entity->getId();
        $native_query->setParameter(1, $NroOrdenCompra, Type::INTEGER);
        $native_query->setParameter(2, $IdCbte, Type::STRING);

        $comprobantes = $native_query->getResult();

        $jsonResult = [];

       
        if( ( $comprobantes ? $comprobantes[0]['cantidad_restante'] : '0') < 0 ) 
        {
            $this->get('session')->getFlashBag()->add('error', 'No se puede anular el comprobante. Alguno de los Items quedar&iacute;a con cantidad negativa.');
            return $this->redirect($this->generateUrl('comprobantes_compra'));
        }
        
        //FIN RUTINA QUE VERIFICA SI SE PUEDE ELIMINAR UN COMPROBANTE
        ///////
        
        if ($entity->getEstadoComprobante() == $estadoAnulado) {
            $em_autenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());
            $usuarioUltimaModificacion = $em_autenticacion->getRepository('ADIFAutenticacionBundle:Usuario')->find($entity->getIdUsuarioUltimaModificacion())->getNombreCompleto();

            $this->get('session')->getFlashBag()->add('error', 'El comprobante ya ha sido anulado el '.$entity->getFechaAnulacion()->format('d/m/Y H:i:s') . ' por el usuario '. $usuarioUltimaModificacion);
            return $this->redirect($this->generateUrl('comprobantes_compra'));
        }

        //$fechaContable = $entity->getFechaContable();
		$fecha_hoy = new DateTime();
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->getEjercicioContableByFecha($fecha_hoy);

        if ($ejercicioContable->getEstaCerrado() || !$ejercicioContable->getMesEjercicioHabilitado($fecha_hoy->format('m'))) {
            $this->get('session')->getFlashBag()->add('error', 'El ejercicio contable est&aacute; cerrado o el mes correspondiente a la fecha contable del comprobante no est&aacute; habilitado');
        } else {

            //verifico los pagos parciales
            $pagosParcialesAnulados = true;
            foreach ($entity->getPagosParciales() as $pagoParcial) {
                $pagosParcialesAnulados &= $pagoParcial->getAnulado();
            }

            if(!$pagosParcialesAnulados) {
                $this->get('session')->getFlashBag()->add('error', 'No se puede anular el comprobante. Existen pagos parciales sin anular.');
                return $this->redirect($this->generateUrl('comprobantes_compra'));
            }

            //$fecha_anulacion = $fecha_hoy->format('Ym') == $fecha_hoy->format('Ym') ? $fecha_hoy : $fechaContable;

            $entity->setEstadoComprobante($estadoAnulado);
            $entity->setFechaAnulacion($fecha_hoy);

            $esNotaCredito = $entity->getEsNotaCredito();
            $esServicio = false;
            $esContraAsiento = !$esNotaCredito;
			$cantidad = $totalOC = 0;
            foreach ($entity->getRenglonesComprobante() as $renglonComprobanteCompra) {
                /* @var $renglonComprobanteCompra \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */
                if ($renglonComprobanteCompra->getIdRenglonOrdenCompra() != null) {
                    $renglonOrdenCompra = $em_compras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                            ->find($renglonComprobanteCompra->getIdRenglonOrdenCompra());
							
					$ordenCompra = $renglonOrdenCompra->getOrdenCompra();
					$totalOC = $ordenCompra->getMonto();

                    if ($renglonOrdenCompra->getRenglonCotizacion() != null) {
                        // Si el comprobante NO es una NotaCredito
						
						$cantidad += $renglonComprobanteCompra->getCantidad();
						
                        if (!$esNotaCredito) {

                            $renglonOrdenCompra->setRestante($renglonOrdenCompra->getRestante() + $renglonComprobanteCompra->getCantidad());
							
                        }
                        // Sino, si es una NotaCredito
                        else {
                            // Si es una Devolucion
                            if ($renglonComprobanteCompra->getEsDevolucion()) {
                                $renglonOrdenCompra->setRestante($renglonOrdenCompra->getRestante() - $renglonComprobanteCompra->getCantidad());
                            }
                            // Sino
                            else {
                                // @todo: bug al anular una NC cuando es devolucion en false
                                // si no es devolucion, la anulacion de una NC no tiene que hacer nada!! - @gluis - 16/03/2018
                                //$renglonOrdenCompra->setRestante($renglonOrdenCompra->getRestante() + $renglonComprobanteCompra->getCantidad());
                            }
                        }
                    } else {
                        $esServicio = true;
                    }
                }
            }
			
			if ($esOcMonedaExtranjera) {
                
                $saldoMonedaExtranjeraOC = 0;
                $totalComprobante = $entity->getTotal();
                $tipoCambio = $entity->getTipoCambio();
                $totalComprobanteMonedaExtranjera = $totalComprobante / $tipoCambio;
                
				if ($esNotaCredito) {
					$saldoMonedaExtranjeraOC = $ordenCompra->getSaldoMonedaExtranjera() - $totalComprobanteMonedaExtranjera;
				} else {
					$saldoMonedaExtranjeraOC = $ordenCompra->getSaldoMonedaExtranjera() + $totalComprobanteMonedaExtranjera;
				}
                //var_dump($ordenCompra->getSaldoMonedaExtranjera(), $totalComprobanteMonedaExtranjera, $saldoMonedaExtranjeraOC); exit;
				$ordenCompra->setSaldoMonedaExtranjera($saldoMonedaExtranjeraOC);
				$em_compras->persist($ordenCompra);
			}
			

            if ($esServicio) {
                // Persisto los asientos contables y presupuestarios
                $numeroAsiento = $this->get('adif.asiento_service')
                        ->generarAsientoServicio($entity, $this->getUser(), $esContraAsiento, $fecha_hoy);
            } else {
                // Persisto los asientos contables y presupuestarios
                $numeroAsiento = $this->get('adif.asiento_service')
                        ->generarAsientoCompras($entity, $this->getUser(), $esContraAsiento, $fecha_hoy);
            }
            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em_compras->flush();

                    $em->getConnection()->commit();

                    $this->get('session')->getFlashBag()->add('success', 'El comprobante fue anulado');

                    $dataArray = [
                        'data-id-comprobante' => $entity->getId(),
                        'data-fecha-asiento' => $fecha_hoy->format('d/m/Y'),
                        'data-es-anulacion' => 1
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();
                    $this->get('session')->getFlashBag()->add('error', 'El comprobante no se pudo anular');

                    throw $e;
                }
            }
        }

        return $this->redirect($this->generateUrl('comprobantes_compra'));
    }

    /**
     * @Route("/generarAsientos/", name="comprobantescompra_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosComprobantesCompra() {

        gc_enable();

        $parcial = false;

        $offset = 0;
        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $comprobantesImportados = $em->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->createQueryBuilder('cc')
                ->where('cc.fechaContable >= :fecha')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->setParameter('fecha', '2015-08-01 00:00:00')
                ->orderBy('cc.id', 'asc')
                ->getQuery()
                ->getResult();

        $offset = $limit * $i;
        $i++;
        while (count($comprobantesImportados) > 0) {
            /* @var $comprobanteImportado ComprobanteCompra */
            foreach ($comprobantesImportados as $comprobanteImportado) {
                /* @var $renglon_oc \ADIF\ComprasBundle\Entity\RenglonOrdenCompra */
                $renglon_oc = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')->find($comprobanteImportado->getRenglonesComprobante()->first()->getIdRenglonOrdenCompra());
                if ($renglon_oc->getRenglonCotizacion() != null) {
                    $this->get('adif.asiento_service')->generarAsientoCompras($comprobanteImportado, $this->getUser(), $comprobanteImportado->getEsNotaCredito());
                } else {
                    $this->get('adif.asiento_service')->generarAsientoServicio($comprobanteImportado, $this->getUser(), $comprobanteImportado->getEsNotaCredito());
                }
                //$emCompras->clear();
            }
            unset($comprobantesImportados);
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            $comprobantesImportados = $em->getRepository('ADIFContableBundle:ComprobanteCompra')
                    ->createQueryBuilder('cc')
                    ->where('cc.fechaContable >= :fecha')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->setParameter('fecha', '2015-08-01 00:00:00')
                    ->orderBy('cc.id', 'asc')
                    ->getQuery()
                    ->getResult();
            $offset = $limit * $i;
            $i++;
        }
        unset($comprobantesImportados);
        $em->clear();
        unset($em);
        gc_collect_cycles();

        if (!$parcial) {
            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de Comprobantes de Compra exitosa');
        }

        return $this->redirect($this->generateUrl('comprobantes_compra'));
    }

    /**
     * 
     * @param ComprobanteCompra $comprobanteCompra
     */
    private function setComprobanteImpresion(ComprobanteCompra $comprobanteCompra) {

		$mensajeError = '';

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $impresionProvincia = "-";
        $impresionLocalidad = "-";
        $impresionCodigoPostal = "-";
        $impresionDomicilio = "-";
        $esExtranjero = false;
        		
        $comprobanteImpresion = new \ADIF\ContableBundle\Entity\ComprobanteImpresion();

        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                ->find($comprobanteCompra->getIdProveedor());

        $domicilioLegal = $proveedor->getClienteProveedor()->getDomicilioLegal();
		$esExtranjero = $proveedor->getClienteProveedor()->getEsExtranjero();
        
		if ($domicilioLegal->getLocalidad() == null && !$esExtranjero) {
			$mensajeError = "El proveedor no tiene una localidad definida en el domicilio legal.";
			return $mensajeError;
		}
		
        $comprobanteImpresion
                ->setRazonSocial($proveedor->getRazonSocial());

        $comprobanteImpresion
                ->setNumeroDocumento($proveedor->getNroDocumento());

        if ( !$esExtranjero ) {            
            $impresionProvincia = $domicilioLegal->getLocalidad()->getProvincia();
            $impresionLocalidad = $domicilioLegal->getLocalidad()->getNombre();
            $impresionCodigoPostal = $domicilioLegal->getCodPostal();
            $impresionDomicilio = $domicilioLegal->__toString();
        }
        
        $comprobanteImpresion->setProvincia($impresionProvincia);
        $comprobanteImpresion->setLocalidad($impresionLocalidad);
        $comprobanteImpresion->setCodigoPostal($impresionCodigoPostal);
        $comprobanteImpresion->setDomicilioLegal($impresionDomicilio);

        $comprobanteCompra
                ->setComprobanteImpresion($comprobanteImpresion);
				
		return $mensajeError;
    }

    /**
     * Tabla para Nota credito.
     *
     * @Route("/index_table_comprobantes/", name="comprobantescompra_index_table_comprobantes")
     * @Method("GET|POST")
     */
    public function indexComprobantesCompraProveedorAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fechaComprobante', 'fechaComprobante');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('letraComprobante', 'letraComprobante');
        $rsm->addScalarResult('puntoVenta', 'puntoVenta');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('numeroOrdenCompra', 'numeroOrdenCompra');
        $rsm->addScalarResult('idProveedor', 'idProveedor');
        $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
        $rsm->addScalarResult('importePendientePago', 'importePendientePago');
        $rsm->addScalarResult('total', 'total');

        $native_query = $em->createNativeQuery('
            SELECT
                id,
                fechaComprobante,
                tipoComprobante,
                letraComprobante,
                puntoVenta,
                numero,
                cuit,
                razonSocial,
                numeroOrdenCompra,
                idProveedor,
                idEstadoComprobante,
                importePendientePago,
                total
            FROM
                vistacomprobantescompra
            WHERE 
                importePendientePago > 0
                AND
                idProveedor = ' . $request->query->get('id_proveedor')
                , $rsm);

        $comprobantes = $native_query->getResult();

        return $this->render('ADIFContableBundle:ComprobanteCompra:index_table_por_proveedor.html.twig', array('comprobantes' => $comprobantes));
    }

    /**
     * Tabla para Nota credito.
     *
     * @Route("/index_table_renglones_comprobantes/", name="comprobantescompra_index_table_renglones_comprobantes")
     * @Method("GET|POST")
     */
    public function indexRenglonesComprobantesCompraAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglones = array();

        $renglonesComprobanteCompra = $em->getRepository('ADIFContableBundle:RenglonComprobanteCompra')
                ->createQueryBuilder('r')
                ->innerJoin('r.comprobante', 'c')
                ->where('c.id IN (:ids)')
                ->setParameter('ids', json_decode($request->request->get('ids_comprobantes', [])))
                ->getQuery()
                ->getResult();

        /* @var $renglon \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */
        foreach ($renglonesComprobanteCompra as $renglon) {
            $renglones[] = array(
                'id' => $renglon->getId(),
                'idRenglonOC' => $renglon->getRenglonOrdenCompra()->getId(),
                'descripcion' => '',
                'denominacionBienEconomico' => $renglon->getDescripcion(),
                'cantidad' => $renglon->getCantidad(),
                'precioUnitario' => $renglon->getPrecioUnitario(),
                'idAlicuotaIva' => $renglon->getAlicuotaIva()->getId(),
                'bonificacionTipo' => $renglon->getBonificacionTipo(),
                'bonificacionValor' => $renglon->getBonificacionValor(),
                'montoNeto' => $renglon->getMontoNeto(),
                'montoIva' => $renglon->getMontoIva(),
                'comprobante' => $renglon->getComprobante()->getPuntoVenta() . '-' . $renglon->getComprobante()->getNumero(),
                'idComprobante' => $renglon->getComprobante()->getId()
            );
        }

        return new JsonResponse($renglones);
    }

}
