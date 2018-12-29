<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use ADIF\ContableBundle\Entity\PagoParcial;
use ADIF\ContableBundle\Form\PagoParcialType;
use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * PagoParcial controller.
 *
 * @Route("/pago_parcial")
 */
class PagoParcialController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Pagos parciales' => $this->generateUrl('pago_parcial')
        );
    }

    /**
     * Lists all PagoParcial entities.
     *
     * @Route("/", name="pago_parcial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Pagos parciales'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Pagos parciales',
            'page_info' => 'Lista de pagos parciales'
        );
    }

    /**
     * Tabla para PagoParcial .
     *
     * @Route("/index_table/", name="pago_parcial_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:PagoParcial')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Pagos parciales'] = null;

        return $this->render('ADIFContableBundle:PagoParcial:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Tabla para Comprobantes.
     *
     * @Route("/index_table_comprobante_proveedor/", name="pago_parcial_table_comprobante_proveedor")
     * @Method("GET|POST")
     */
    public function indexTableComprobanteByProveedorAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobantes = [];

        $comprobantesNoValidos = [
            ConstanteTipoComprobanteObra::NOTA_CREDITO,
            ConstanteTipoComprobanteObra::NOTA_DEBITO_INTERESES,
            ConstanteTipoComprobanteObra::CUPON
        ];

        // COMPROBANTES DE COMPRA *****/

        $comprobantesCompra = $em->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->createQueryBuilder('c')
                ->innerJoin('c.tipoComprobante', 'tc')
                ->where('c.idProveedor = :idProveedor')
                ->andWhere('tc.id NOT IN (:comprobantesNoValidos)')
                ->andWhere('c.ordenPago IS NULL')
                ->setParameter('idProveedor', $request->request->get('id_proveedor'))
                ->setParameter('comprobantesNoValidos', $comprobantesNoValidos, Connection::PARAM_STR_ARRAY)
                ->orderBy('c.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();

        // Por cada comprobante de compra
        foreach ($comprobantesCompra as $comprobante) {

            $comprobantes[] = $this->getComprobanteData($comprobante);
        }


        // COMPROBANTES DE OBRA *****/

        $comprobantesObra = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')
                ->createQueryBuilder('c')
                ->innerJoin('c.tipoComprobante', 'tc')
                ->where('c.idProveedor = :idProveedor')
                ->andWhere('tc.id NOT IN (:comprobantesNoValidos)')
                ->andWhere('c.ordenPago IS NULL')
                ->setParameter('idProveedor', $request->request->get('id_proveedor'))
                ->setParameter('comprobantesNoValidos', $comprobantesNoValidos, Connection::PARAM_STR_ARRAY)
                ->orderBy('c.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();

        // Por cada comprobante de obra
        foreach ($comprobantesObra as $comprobante) {

            $comprobantes[] = $this->getComprobanteData($comprobante);
        }


        return $this->render('ADIFContableBundle:PagoParcial:index_table_comprobante_por_proveedor.html.twig', array('comprobantes' => $comprobantes));
    }

    /**
     * Creates a new PagoParcial entity.
     *
     * @Route("/insertar", name="pago_parcial_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:PagoParcial:new.html.twig")
     */
    public function createAction(Request $request) {

        $pagoParcial = new PagoParcial();

        $form = $this->createCreateForm($pagoParcial);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
			$emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

            $idComprobante = $request->request->get('id_comprobante');

            $comprobante = $em->getRepository('ADIFContableBundle:Comprobante')
                    ->find($idComprobante);

            if (!$comprobante) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
            }

            // Seteo el Comprobante
            $pagoParcial->setComprobante($comprobante);


            // Genero la AutorizacionContable            
            $ordenPagoService = $this->get('adif.orden_pago_service');

            $idProveedor = $pagoParcial->getIdProveedor();
			$proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

            $fechaAutorizacionContable = $pagoParcial->getFechaPago();

            $importe = $pagoParcial->getImporte();

            $detalleConcepto = '';

			$esComprobanteObra =  false;
			
            if ($comprobante->getEsComprobanteObra()) {
				
				$esComprobanteObra =  true;
				
                $detalleConcepto = $comprobante->getTramo();
				
            } elseif ($comprobante->getEsComprobanteCompra()) {

                $detalleConcepto = 'OC n&deg; ' . $comprobante->getOrdenCompra()->getNumeroOrdenCompra();
            }

            $concepto = 'Pago parcial de ' . $comprobante->getTipoComprobante()
                    . ' (' . $comprobante->getLetraComprobante() . ') '
                    . $comprobante->getNumeroCompleto()
                    . ' - ' . $detalleConcepto;

            /* @var $autorizacionContable OrdenPagoGeneral */
            $autorizacionContable = $ordenPagoService
                    ->crearAutorizacionContablePagoParcial($em, $pagoParcial, $importe, $concepto);
					
            $autorizacionContable->setFechaAutorizacionContable($fechaAutorizacionContable);
            $autorizacionContable->setIdProveedor($idProveedor);
			$autorizacionContable->setProveedor($proveedor);

			/** Inicio las retenciones **/
			try {
				
				$error = $this
							->get('adif.retenciones_service')
							->generarComprobantesRetencionPagoParcial($autorizacionContable, $esComprobanteObra);
			} catch(\Exception $e) {
				//var_dump($e->getTraceAsString());exit;
				$this->get('session')->getFlashBag()
                    ->add('error', "Hubo un error al calcular las retenciones");
					
				return $this->redirect($this->generateUrl('pago_parcial'));
			}
			
			if ($this->mostrarErrorExencion($error, $autorizacionContable->getBeneficiario()->getControllerPath(), $autorizacionContable->getBeneficiario()->getId())) {
				return $this->redirect($this->generateUrl('pago_parcial'));
			}
			/** Fin retenciones **/
			
			$pagoParcial->setOrdenPago($autorizacionContable);
			
			$comprobantesRetencionImpuesto = $autorizacionContable->getRetenciones();
			
			$montoTotalRetenciones = 0;
			$retencionSuss = 0;
			$retencionIibb = 0;
			$retencionGanancias = 0;
			$retencionIva = 0;
			foreach($comprobantesRetencionImpuesto as $comprobanteRetencionImpuesto) {
				
				$monto = $comprobanteRetencionImpuesto->getMonto();
				$montoTotalRetenciones += $monto;
				
				$denominacionTipoImpuesto = $comprobanteRetencionImpuesto->getRegimenRetencion()->getTipoImpuesto()->getDenominacion();
				switch ($denominacionTipoImpuesto) {
					case ConstanteTipoImpuesto::SUSS:
						$retencionSuss += $monto;
						break;
					case ConstanteTipoImpuesto::IIBB:
						$retencionIibb += $monto;
						break;
					case ConstanteTipoImpuesto::Ganancias:
						$retencionGanancias += $monto;
						break;
					case ConstanteTipoImpuesto::IVA: 
						$retencionIva += $monto;
						break;
				}
			}
			
			$pagoParcial->setRetencionSuss($retencionSuss);
			$pagoParcial->setRetencionIibb($retencionIibb);
			$pagoParcial->setRetencionGanancias($retencionGanancias);
			$pagoParcial->setRetencionIva($retencionIva);
			
			$pagoParcial->setMontoTotalRetenciones($montoTotalRetenciones);
			$pagoParcial->setDiferenciaImporteRetenciones(0);
			if ($importe != $montoTotalRetenciones) {
				$diferencia = $importe - $montoTotalRetenciones;
				$pagoParcial->setDiferenciaImporteRetenciones($diferencia);
			}
			
			//\Doctrine\Common\Util\Debug::dump( $montoTotalRetenciones ); exit;
			//die("Termino");
            $em->persist($pagoParcial);
            $em->flush();

            $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                    . $this->generateUrl($autorizacionContable->getPathAC() . '_print', ['id' => $autorizacionContable->getId()])
                    . '" class="link-imprimir-op">aqu&iacute;</a>';

            $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);

            return $this->redirect($this->generateUrl('pago_parcial'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $pagoParcial,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear pago parcial',
        );
    }

    /**
     * Creates a form to create a PagoParcial entity.
     *
     * @param PagoParcial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PagoParcial $entity) {
        $form = $this->createForm(new PagoParcialType(), $entity, array(
            'action' => $this->generateUrl('pago_parcial_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new PagoParcial entity.
     *
     * @Route("/crear", name="pago_parcial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new PagoParcial();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear pago parcial'
        );
    }

    /**
     * Finds and displays a PagoParcial entity.
     *
     * @Route("/{id}", name="pago_parcial_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:PagoParcial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PagoParcial.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Pago parcial'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver pago parcial'
        );
    }

    /**
     * Displays a form to edit an existing PagoParcial entity.
     *
     * @Route("/editar/{id}", name="pago_parcial_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:PagoParcial:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:PagoParcial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PagoParcial.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar pago parcial'
        );
    }

    /**
     * Creates a form to edit a PagoParcial entity.
     *
     * @param PagoParcial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PagoParcial $entity) {
        $form = $this->createForm(new PagoParcialType(), $entity, array(
            'action' => $this->generateUrl('pago_parcial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing PagoParcial entity.
     *
     * @Route("/actualizar/{id}", name="pago_parcial_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:PagoParcial:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:PagoParcial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PagoParcial.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pago_parcial'));
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
            'page_title' => 'Editar pago parcial'
        );
    }

    /**
     * Deletes a PagoParcial entity.
     *
     * @Route("/borrar/{id}", name="pago_parcial_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:PagoParcial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PagoParcial.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('pago_parcial'));
    }

    /**
     * 
     * @param type $comprobante
     * @return type
     */
    private function getComprobanteData($comprobante) {

        $nombreComprobante = $comprobante->getTipoComprobante()->__toString()
                . ' (' . $comprobante->getLetraComprobante()->__toString() . ') '
                . ' - ' . $comprobante->getNumeroCompleto();

        return array(
            'id' => $comprobante->getId(),
            'fechaComprobante' => $comprobante->getFechaComprobante()->format('d/m/Y'),
            'comprobante' => $nombreComprobante,
            'numeroReferencia' => $comprobante->getNumeroReferencia() != null ? $comprobante->getNumeroReferencia() : '-',
            'importeTotal' => $comprobante->getSaldo()
        );
    }
	
	/**
     * 
     * @param type $error
     * @param type $path
     * @param type $id
     * @return boolean
     */
    public function mostrarErrorExencion($error, $path, $id) {

        if ($error != null) {
            if ($error['error']) {
                $errorMsg = '<span> Existen certificados de exenci&oacute;n vencidos:</span>';
                $errorMsg .= '<div style="padding-left: 3em; margin-top: .5em">';
                $errorMsg .= '<ul>';

                if ($error[ConstanteTipoImpuesto::Ganancias]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::Ganancias . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::IVA]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::IVA . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::SUSS]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::SUSS . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::IIBB]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::IIBB . '</a></li>';
                }
                $errorMsg .= '</ul>';
                $errorMsg .= '</div>';
                if ($error['limitaGeneracion']) {
                    $this->get('session')->getFlashBag()
                            ->add('error', "<span>No se puede generar la autorización contable.</span>" . $errorMsg);
                    return true;
                } else {
                    $this->get('session')->getFlashBag()
                            ->add('warning', "<span>Si bien no corresponde retenci&oacute;n.</span>" . $errorMsg);
                    return false;
                }
            }
        }
    }

}
