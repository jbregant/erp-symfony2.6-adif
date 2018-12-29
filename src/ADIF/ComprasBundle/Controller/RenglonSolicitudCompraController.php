<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\ComprasBundle\Entity\RenglonSolicitudCompra;
use ADIF\ComprasBundle\Form\RenglonSolicitudCompraType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * RenglonSolicitudCompra controller.
 *
 * @Route("/renglonsolicitudcompra")
 */
class RenglonSolicitudCompraController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'RenglonSolicitudCompra' => $this->generateUrl('renglonsolicitudcompra')
        );
    }

    /**
     * Lists all RenglonSolicitudCompra entities.
     *
     * @Route("/", name="renglonsolicitudcompra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['RenglonSolicitudCompra'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'RenglonSolicitudCompra',
            'page_info' => 'Lista de renglonsolicitudcompra'
        );
    }

    /**
     * Creates a new RenglonSolicitudCompra entity.
     *
     * @Route("/insertar", name="renglonsolicitudcompra_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:RenglonSolicitudCompra:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new RenglonSolicitudCompra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('renglonsolicitudcompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear RenglonSolicitudCompra',
        );
    }

    /**
     * Creates a form to create a RenglonSolicitudCompra entity.
     *
     * @param RenglonSolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RenglonSolicitudCompra $entity) {
        $form = $this->createForm(new RenglonSolicitudCompraType($this->get('security.context'), 
                                                                 $this->getDoctrine()->getManager($this->getEntityManager())), 
            $entity, array(
            'action' => $this->generateUrl('renglonsolicitudcompra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new RenglonSolicitudCompra entity.
     *
     * @Route("/crear", name="renglonsolicitudcompra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new RenglonSolicitudCompra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear RenglonSolicitudCompra'
        );
    }

    /**
     * Finds and displays a RenglonSolicitudCompra entity.
     *
     * @Route("/{id}", name="renglonsolicitudcompra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['RenglonSolicitudCompra'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver RenglonSolicitudCompra'
        );
    }

    /**
     * Displays a form to edit an existing RenglonSolicitudCompra entity.
     *
     * @Route("/editar/{id}", name="renglonsolicitudcompra_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:RenglonSolicitudCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar RenglonSolicitudCompra'
        );
    }

    /**
     * Creates a form to edit a RenglonSolicitudCompra entity.
     *
     * @param RenglonSolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RenglonSolicitudCompra $entity) {
        $form = $this->createForm(new RenglonSolicitudCompraType($this->get('security.context'), 
                                                                 $this->getDoctrine()->getManager($this->getEntityManager())), 
            $entity, array(
            'action' => $this->generateUrl('renglonsolicitudcompra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing RenglonSolicitudCompra entity.
     *
     * @Route("/actualizar/{id}", name="renglonsolicitudcompra_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:RenglonSolicitudCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('renglonsolicitudcompra'));
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
            'page_title' => 'Editar RenglonSolicitudCompra'
        );
    }

    /**
     * Deletes a RenglonSolicitudCompra entity.
     *
     * @Route("/borrar/{id}", name="renglonsolicitudcompra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('renglonsolicitudcompra'));
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws type
     * 
     * @Route("/check-usuario", name="check-usuario")
     */
    public function checkUsuarioUsandoloAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $emAutenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());

        $continuarEjecucion = true;

        $idUsuario = null;
        $nombreUsuario = null;

        $idRenglonSolicitud = $request->request->get('id_renglon_solicitud');

        $renglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($idRenglonSolicitud);

        if (!$renglonSolicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        // Si el RenglonSolicitud está siento utilizado por algun usuario
        if (null != $renglonSolicitudCompra->getIdUsuarioUsandolo()) {

            // Obtengo el usuario asociado al RenglonSolicitud
            $usuario = $emAutenticacion->getRepository('ADIFAutenticacionBundle:Usuario')
                    ->find($renglonSolicitudCompra->getIdUsuarioUsandolo());

            // Si el usuario que lo está usando NO es el usuario logueado
            if ($this->getUser()->getId() != $renglonSolicitudCompra->getIdUsuarioUsandolo()) {

                $idUsuario = $usuario->getId();
                $nombreUsuario = $usuario->getNombre() . ' ' . $usuario->getApellido();

                $continuarEjecucion = false;
            }
        } //.
        else {
            $renglonSolicitudCompra->setUsuarioUsandolo($this->getUser());
        }

        $em->flush();

        $data = array(
            'continuarEjecucion' => $continuarEjecucion,
            'idUsuario' => $idUsuario,
            'nombreUsuario' => $nombreUsuario)
        ;

        return new JsonResponse($data);
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws type
     * 
     * @Route("/clear-usuario", name="clear-usuario")
     */
    public function clearUsuarioUsandoloAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idRenglonSolicitud = $request->request->get('id_renglon_solicitud');

        $renglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($idRenglonSolicitud);

        if (!$renglonSolicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        $renglonSolicitudCompra->setUsuarioUsandolo(null);

        $em->flush();

        return new JsonResponse();
    }
	
	/**
     * Edita la descrpcion del renglon
     *
     * @Route("/editar_descripcion", name="renglonsolicitudcompra_descripcion_update")
     * @Method("POST")
     */
    public function updateDescripcionAction(Request $request) {
		
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$id = $request->get('id');
		$descripcion = $request->get('descripcion');
		
        $entity = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);
		
		$res = array();
		
        if (!$entity) {
			$res['status'] = 'nok';
			$res['mensaje'] = 'No se pudo encontrar el renglon de la solicitud de compra.';
			return new JsonResponse($res);
        }
		
        $entity->setDescripcion($descripcion);
		
		try {
			
			$em->persist($entity);
			$em->flush();
			
			$res['status'] = 'ok';
			$res['mensaje'] = 'Se ha podido grabar con exito la descripción del renglon de la solicitud de compra.';
			$res['descripcion'] = $descripcion;
			
		} catch(\Excepcion $e) {
			$res['status'] = 'nok';
			$res['mensaje'] = 'No se ha podido grabar la descripción del renglon de la solicitud de compra.';
		}
		
		return new JsonResponse($res);
    }

}
