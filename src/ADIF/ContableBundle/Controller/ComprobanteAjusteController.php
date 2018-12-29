<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ComprobanteAjuste;
use ADIF\ContableBundle\Form\ComprobanteAjusteType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use Symfony\Component\HttpFoundation\JsonResponse;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * ComprobanteAjuste controller.
 *
 * @Route("/comprobante_ajuste")
  */
class ComprobanteAjusteController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'ComprobanteAjuste' => $this->generateUrl('comprobante_ajuste')
        );
    }
    /**
     * Lists all ComprobanteAjuste entities.
     *
     * @Route("/", name="comprobante_ajuste")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['ComprobanteAjuste'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'ComprobanteAjuste',
            'page_info' => 'Lista de comprobanteajuste'
        );
    }

    /**
     * Tabla para ComprobanteAjuste .
     *
     * @Route("/index_table/", name="comprobante_ajuste_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ComprobanteAjuste')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['ComprobanteAjuste'] = null;

    return $this->render('ADIFContableBundle:ComprobanteAjuste:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new ComprobanteAjuste entity.
     *
     * @Route("/insertar", name="comprobante_ajuste_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ComprobanteAjuste();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('comprobante_ajuste'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear ComprobanteAjuste',
        );
    }

    /**
    * Creates a form to create a ComprobanteAjuste entity.
    *
    * @param ComprobanteAjuste $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ComprobanteAjuste $entity)
    {
        $form = $this->createForm(new ComprobanteAjusteType(), $entity, array(
            'action' => $this->generateUrl('comprobante_ajuste_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ComprobanteAjuste entity.
     *
     * @Route("/crear", name="comprobante_ajuste_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ComprobanteAjuste();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear ComprobanteAjuste'
        );
}

    /**
     * Finds and displays a ComprobanteAjuste entity.
     *
     * @Route("/{id}", name="comprobante_ajuste_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteAjuste')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteAjuste.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['ComprobanteAjuste'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver ComprobanteAjuste'
        );
    }

    /**
     * Displays a form to edit an existing ComprobanteAjuste entity.
     *
     * @Route("/editar/{id}", name="comprobante_ajuste_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteAjuste')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteAjuste.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar ComprobanteAjuste'
        );
    }

    /**
    * Creates a form to edit a ComprobanteAjuste entity.
    *
    * @param ComprobanteAjuste $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ComprobanteAjuste $entity)
    {
        $form = $this->createForm(new ComprobanteAjusteType(), $entity, array(
            'action' => $this->generateUrl('comprobante_ajuste_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing ComprobanteAjuste entity.
     *
     * @Route("/actualizar/{id}", name="comprobante_ajuste_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteAjuste')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteAjuste.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprobante_ajuste'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar ComprobanteAjuste'
        );
    }
   
	/**
     *
     * @Route("/proveedor/crear", name="new_ajuste_proveedor")
     * @Method("GET")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.ajuste_cuenta_corriente_proveedor.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_PLIEGOS_COMPRA')")
    public function newComprobanteAjusteCuentaCorrienteProveedorAction() {

        $comprobanteAjuste = new ComprobanteAjuste();

        $form = $this->createCreateAjusteCuentaCorrienteProveedorForm($comprobanteAjuste);
        
        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('new_ajuste_proveedor');
        $bread['Crear comprobante de ajuste'] = null;

		return array(
            'entity' => $comprobanteAjuste,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de ajuste proveedores',
        );
    }

    /**
     * 
     * @param ComprobanteVenta $entity
     * @return type
     */
    private function createCreateAjusteCuentaCorrienteProveedorForm(ComprobanteAjuste $entity) {
        $form = $this->createForm(new ComprobanteAjusteType(), $entity, array(
            'action' => $this->generateUrl('create_ajuste_proveedor'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
     /**
     * Creates a new ComprobanteVenta entity.
     *
     * @Route("/ajuste_cuenta_corriente_proveedor/insertar", name="create_ajuste_proveedor")
     * @Method("POST")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.ajuste_cuenta_corriente_proveedor.html.twig")
     */
    public function createAjusteCuentaCorrienteProveedorAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $idTipoComprobante = $request->request->get('adif_contablebundle_comprobanteajuste', false)['tipoComprobante'];
        $idProveedor = $request->request->get('adif_contablebundle_comprobanteajuste', false)['idProveedor'];
		$idComprobante = $request->request->get('adif_contablebundle_comprobanteajuste', false)['idComprobante'];
        $fechaComprobante = $request->request->get('adif_contablebundle_comprobanteajuste', false)['fechaComprobante'];
		$observaciones = $request->request->get('adif_contablebundle_comprobanteajuste', false)['observaciones'];
		$total = $request->request->get('adif_contablebundle_comprobanteajuste', false)['total'];
		
        $comprobante = $em->getRepository('ADIFContableBundle:Comprobante')->find($idComprobante);
        if (!$comprobante) {
			throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
		}
		
        $total = str_replace(',', '.', $total);
		
//		if ($total > $comprobante->getSaldoALaFecha(new \DateTime())) { 
//			$this->get('session')->getFlashBag()->add('error', 'El total no puede ser mayor que el saldo del comprobante.');
//			return $this->redirect($this->generateUrl('new_ajuste_proveedor'));
//		}
		
		if ($total <= 0) {
			$this->get('session')->getFlashBag()->add('error', 'El total tiene que ser mayor a cero.');
			return $this->redirect($this->generateUrl('new_ajuste_proveedor'));
		}
        
        $detalle = null;
		$comprobanteAjuste = new ComprobanteAjuste();
        if ($idTipoComprobante == ConstanteTipoComprobanteCompra::NOTA_DEBITO) {
            $detalle = 'Ajuste débito (Y)';
			$comprobanteAjuste->setEsNotaCredito(false);
        }
        
        if ($idTipoComprobante == ConstanteTipoComprobanteCompra::NOTA_CREDITO) {
            $detalle = 'Ajuste crédito (Y)';
			$comprobanteAjuste->setEsNotaCredito(true);
        }
        
        $form = $this->createCreateAjusteCuentaCorrienteProveedorForm($comprobanteAjuste);
        $form->handleRequest($request);

        if ($form->isValid()) {
			$comprobanteAjuste->setComprobante($comprobante);
            $comprobanteAjuste->setTotal($total);
            $comprobanteAjuste->setIdProveedor($idProveedor);
			$comprobanteAjuste->setIdCliente(null);
            $comprobanteAjuste->setFechaComprobante(\DateTime::createFromFormat('d/m/Y', $fechaComprobante));
			$comprobanteAjuste->setDetalle($detalle);
            $comprobanteAjuste->setObservaciones($observaciones);
			
            try {
                $em->persist($comprobanteAjuste);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                            'success', 'El comprobante de ajuste "Y" se dió de alta con &eacute;xito.'
                );
				
				return $this->redirect($this->generateUrl('new_ajuste_proveedor'));
				
                
            } catch(Exception $e) {
                $this->get('session')->getFlashBag()->add(
                           'error', 'El comprobante de ajuste "Y" no se pudo crear. Intentelo más tarde.'
                   );
            }
               
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('new_ajuste_proveedor');
        $bread['Crear comprobante de ajuste'] = null;
        
       
        return array(
            'entity' => $comprobante,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de ajuste proveedores',
        );
    }
	
	/** Cliente **/
	
	/**
     *
     * @Route("/cliente/crear", name="new_ajuste_cliente")
     * @Method("GET")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.ajuste_cuenta_corriente_cliente.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_PLIEGOS_COMPRA')")
    public function newComprobanteAjusteCuentaCorrienteClienteAction() {

        $comprobanteAjuste = new ComprobanteAjuste();

        $form = $this->createCreateAjusteCuentaCorrienteClienteForm($comprobanteAjuste);
        
        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('new_ajuste_cliente');
        $bread['Crear comprobante de ajuste'] = null;

		return array(
            'entity' => $comprobanteAjuste,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de ajuste clientees',
        );
    }

    /**
     * 
     * @param ComprobanteVenta $entity
     * @return type
     */
    private function createCreateAjusteCuentaCorrienteClienteForm(ComprobanteAjuste $entity) {
        $form = $this->createForm(new ComprobanteAjusteType(), $entity, array(
            'action' => $this->generateUrl('create_ajuste_cliente'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
     /**
     * Creates a new ComprobanteVenta entity.
     *
     * @Route("/ajuste_cuenta_corriente_cliente/insertar", name="create_ajuste_cliente")
     * @Method("POST")
     * @Template("ADIFContableBundle:ComprobanteAjuste:new.ajuste_cuenta_corriente_cliente.html.twig")
     */
    public function createAjusteCuentaCorrienteClienteAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $idTipoComprobante = $request->request->get('adif_contablebundle_comprobanteajuste', false)['tipoComprobante'];
        $idCliente = $request->request->get('adif_contablebundle_comprobanteajuste', false)['idCliente'];
		$idComprobante = $request->request->get('adif_contablebundle_comprobanteajuste', false)['idComprobante'];
        $fechaComprobante = $request->request->get('adif_contablebundle_comprobanteajuste', false)['fechaComprobante'];
		$observaciones = $request->request->get('adif_contablebundle_comprobanteajuste', false)['observaciones'];
		$total = $request->request->get('adif_contablebundle_comprobanteajuste', false)['total'];
		
        $comprobante = $em->getRepository('ADIFContableBundle:Comprobante')->find($idComprobante);
        if (!$comprobante) {
			throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
		}
		
		$total = str_replace(',', '.', $total);
		
//		if ($total > $comprobante->getSaldoALaFecha(new \DateTime())) {
//			$this->get('session')->getFlashBag()->add('error', 'El total no puede ser mayor que el saldo del comprobante.');
//			return $this->redirect($this->generateUrl('new_ajuste_cliente'));
//		}
		
		if ($total <= 0) {
			$this->get('session')->getFlashBag()->add('error', 'El total tiene que ser mayor a cero.');
			return $this->redirect($this->generateUrl('new_ajuste_cliente'));
		}
		
		if ($total <= 0) {
			$this->get('session')->getFlashBag()->add('error', 'El total tiene que ser mayor a cero.');
			return $this->redirect($this->generateUrl('new_ajuste_cliente'));
		}
			
        $detalle = null;
		$comprobanteAjuste = new ComprobanteAjuste();
        if ($idTipoComprobante == ConstanteTipoComprobanteCompra::NOTA_DEBITO) {
            $detalle = 'Ajuste débito (Y)';
			$comprobanteAjuste->setEsNotaCredito(false);
        }
        
        if ($idTipoComprobante == ConstanteTipoComprobanteCompra::NOTA_CREDITO) {
            $detalle = 'Ajuste crédito (Y)';
			$comprobanteAjuste->setEsNotaCredito(true);
        }
		
		// Si al comprobante que quiero ajustar, es un cupon, lo dejo anulado y con fecha de anulacion de fecha del comprobante
		if ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::CUPON) {
			$estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);
			$comprobante->setEstadoComprobante($estadoAnulado);
			$comprobante->setFechaAnulacion(\DateTime::createFromFormat('d/m/Y', $fechaComprobante));
		}
        
        $form = $this->createCreateAjusteCuentaCorrienteClienteForm($comprobanteAjuste);
        $form->handleRequest($request);

        if ($form->isValid()) {
			$comprobanteAjuste->setComprobante($comprobante);
            $comprobanteAjuste->setTotal($total);
            $comprobanteAjuste->setIdCliente($idCliente);
			$comprobanteAjuste->setIdProveedor(null);
            $comprobanteAjuste->setFechaComprobante(\DateTime::createFromFormat('d/m/Y', $fechaComprobante));
			$comprobanteAjuste->setDetalle($detalle);
            $comprobanteAjuste->setObservaciones($observaciones);
			
            try {
				$em->persist($comprobante);
                $em->persist($comprobanteAjuste);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                            'success', 'El comprobante de ajuste "Y" se dió de alta con &eacute;xito.'
                );
				
				return $this->redirect($this->generateUrl('new_ajuste_cliente'));
				
                
            } catch(Exception $e) {
                $this->get('session')->getFlashBag()->add(
                           'error', 'El comprobante de ajuste "Y" no se pudo crear. Intentelo más tarde.'
                   );
            }
               
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('new_ajuste_cliente');
        $bread['Crear comprobante de ajuste'] = null;
        
       
        return array(
            'entity' => $comprobante,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de ajuste clientees',
        );
    }
	
	/**
     * Anula el comprobante de ajuste.
     *
     * @Route("/anular/{id}", name="comprobante_ajuste_anular")
     * @Method("GET")
	 * @Security("has_role('ROLE_ANULAR_COMPROBANTE_AJUSTE')")
     */ 
    public function anularComprobanteAjuste($id) 
	{
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteAjuste')->find($id);
		
		if (!$entity) {
			throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteAjuste.');
		}
		
		$entity->setFechaBaja(new \DateTime());
		
		$em->persist($entity);
		$em->flush();
		
		$this->get('session')->getFlashBag()->add('success', 'El comprobante de ajuste "Y" se borro con &eacute;xito.');

		if ($entity->getIdCliente() != null) {
			return $this->redirect($this->generateUrl('cliente_cta_cte_detalle_total', array('idCliente' => $entity->getIdCliente() )  ));
		} else {
			return $this->redirect($this->generateUrl('proveedor_cta_cte_detalle_total', array('idProveedor' => $entity->getIdProveedor() )  ));
		}
		
    }
}
