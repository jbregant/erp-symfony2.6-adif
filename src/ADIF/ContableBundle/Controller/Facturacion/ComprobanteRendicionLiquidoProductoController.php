<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Form\Facturacion\ComprobanteRendicionLiquidoProductoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use \Doctrine\Common\Util\Debug;

/**
 * Facturacion\ComprobanteRendicionLiquidoProductoController controller.
 *
 * @Route("/comprobanteventa/comprobante_rendicion_liquido_producto")
 */
class ComprobanteRendicionLiquidoProductoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Rendici&oacute;n liquido producto' => $this->generateUrl('comprobante_rendicion_liquido_producto_new')
        );
    }

    /**
     * Creates a new Facturacion\ComprobanteRendicionLiquidoProducto entity.
     *
     * @Route("/insertar", name="comprobante_rendicion_liquido_producto_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto:new.html.twig")
     */
    public function createAction(Request $request) {
		
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
		
        $entity = new ComprobanteRendicionLiquidoProducto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
		
        if ($form->isValid()) {
			
			$requestForm = $request->get('adif_contablebundle_comprobanterendicionliquidoproducto');
			
            // Seteo el Estado
            $entity->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            $entity->setSaldo($entity->getTotal());
            
            $esContraAsiento = false;
            if ($entity->getTotal() < 0) {
                $esContraAsiento = true;
            }
			
			// Seteo el Cliente
            if ($requestForm['idCliente']) {

                $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')
                        ->find($requestForm['idCliente']);

                $entity->setCliente($cliente);

                //$this->setComprobanteImpresion($entity);
            }
			
			// Genero el asiento contable y presupuestario
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientosComprobanteRendicionLiquidoProducto(
						$entity, 
						$this->getUser(), 
						$offsetNumeroAsiento = 0, 
						$esContraAsiento
					);
			
			// Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
					
                    $em->flush();

                    $em->getConnection()->commit();

                    $dataArray = [
                        'data-id-comprobante' => $entity->getId(),
                    ];

					$this->addSuccessFlash('Se dio de alta correctamente la rendici&oacute;n l&iacute;quido producto.');
			
                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
							
					return $this->redirect($this->generateUrl('comprobante_rendicion_liquido_producto_new'));
					
                } catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
				
            } else {
				
				$this->addErrorFlash('No se pudo dar de alta la rendici&ocute;n l&iacute;quido producto.');
				
				return $this->redirect($this->generateUrl('comprobante_rendicion_liquido_producto_new'));
				
			}
			
        } else {
			
			$this->addErrorFlash('No se pudo dar de alta la rendici&ocute;n l&iacute;quido producto.');
			
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rendici&ocute;n l&iacute;quido producto',
        );
    }

    /**
     * Creates a form to create a Facturacion\ConceptoVentaGeneral entity.
     *
     * @param ConceptoVentaGeneral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ComprobanteRendicionLiquidoProducto $entity) {
        $form = $this->createForm(new ComprobanteRendicionLiquidoProductoType(), $entity, array(
            'action' => $this->generateUrl('comprobante_rendicion_liquido_producto_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/crear", name="comprobante_rendicion_liquido_producto_new")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto:new.html.twig")
     */
    public function newAction() {
        $entity = new ComprobanteRendicionLiquidoProducto();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rendici&oacute;n l&iacute;quido producto'
        );
    }

    /**
     * Finds and displays a Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/{id}", name="comprobante_rendicion_liquido_producto_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto:show.html.twig")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteRendicionLiquidoProducto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobante rendicion liquido producto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante rendicion liquido producto'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/editar/{id}", name="comprobante_rendicion_liquido_producto_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteRendicionLiquidoProducto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rendici&oacute;n l&iacute;quido producto'
        );
    }

    /**
     * Creates a form to edit a Facturacion\ConceptoVentaGeneral entity.
     *
     * @param ConceptoVentaGeneral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    
    private function createEditForm(ComprobanteRendicionLiquidoProducto $entity) {
        $form = $this->createForm(new ComprobanteRendicionLiquidoProductoType(), $entity, array(
            'action' => $this->generateUrl('comprobante_rendicion_liquido_producto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/actualizar/{id}", name="comprobante_rendicion_liquido_producto_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteRendicionLiquidoProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteRendicionLiquidoProducto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprobante_rendicion_liquido_producto_new'));
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
            'page_title' => 'Editar concepto de venta general'
        );
    }
}
