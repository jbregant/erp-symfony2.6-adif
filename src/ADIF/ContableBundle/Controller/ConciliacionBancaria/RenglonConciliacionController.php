<?php

namespace ADIF\ContableBundle\Controller\ConciliacionBancaria;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion;
use ADIF\ContableBundle\Form\ConciliacionBancaria\RenglonConciliacionType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonConciliacion;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ConciliacionBancaria\RenglonConciliacion controller.
 *
 * @Route("/renglonesconciliacion")
 */
class RenglonConciliacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Renglones de conciliaci&oacute;n' => $this->generateUrl('renglonesconciliacion')
        );
    }

    /**
     * Lists all ConciliacionBancaria\RenglonConciliacion entities.
     *
     * @Route("/", name="renglonesconciliacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Renglones de conciliaci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Renglones de conciliaci&oacute;n',
            'page_info' => 'Lista de renglones de conciliaci&oacute;n'
        );
    }

    /**
     * Creates a new ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @Route("/insertar", name="renglonesconciliacion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new RenglonConciliacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('renglonesconciliacion'));
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
            'page_title' => 'Crear rengl&oacute;n de conciliaci&oacute;n',
        );
    }

    /**
     * Creates a form to create a ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @param RenglonConciliacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RenglonConciliacion $entity) {
        $form = $this->createForm(new RenglonConciliacionType(), $entity, array(
            'action' => $this->generateUrl('renglonesconciliacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @Route("/crear", name="renglonesconciliacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new RenglonConciliacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Finds and displays a ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @Route("/{id}", name="renglonesconciliacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\RenglonConciliacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Rengl&oacute;n de conciliaci&oacute;n'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @Route("/editar/{id}", name="renglonesconciliacion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\RenglonConciliacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @param RenglonConciliacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RenglonConciliacion $entity) {
        $form = $this->createForm(new RenglonConciliacionType(), $entity, array(
            'action' => $this->generateUrl('renglonesconciliacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @Route("/actualizar/{id}", name="renglonesconciliacion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\RenglonConciliacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('renglonesconciliacion'));
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
            'page_title' => 'Editar rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Deletes a ConciliacionBancaria\RenglonConciliacion entity.
     *
     * @Route("/borrar/{id}", name="renglonesconciliacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\RenglonConciliacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('renglonesconciliacion'));
    }

    /**
     * Tabla para extractos.
     *
     * @Route("/index_table/", name="renglones_conciliacion_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {
        //var_dump($request->query->get('id_conciliacion'));die;
        $renglones = $this->obtenerRenglones(ConstanteEstadoRenglonConciliacion::ESTADO_PENDIENTE, $request->query->get('id_conciliacion'));

        return $this->render('ADIFContableBundle:ConciliacionBancaria/RenglonConciliacion:index_table.html.twig', array('entities' => $renglones));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/asignar_conceptos/", name="renglones_conciliacion_asignar_conceptos")
     * @Method("GET|POST")
     */
    public function asignarConceptosAction(Request $request) {

        $idsMovimientos = json_decode($request->request->get('ids', '[]'));
        $idConcepto = json_decode($request->request->get('concepto'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $concepto = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->findOneById($idConcepto);

        if (!$concepto) {
            //throw $this->createNotFoundException('No se puede encontrar el ConceptoConciliacion.');
            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => 'No se puede encontrar el ConceptoConciliacion.'));
        }

        foreach ($idsMovimientos as $idMovimiento) {
            $movimiento = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($idMovimiento);

            if (!$movimiento) {
                //throw $this->createNotFoundException('No se puede encontrar el RenglonConciliacion.');
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'No se puede encontrar el RenglonConciliacion.'));
            }

            $movimiento->setConceptoConciliacion($concepto);
        }

        $em->flush();

        /*
          $response = new Response();
          $response->setStatusCode(Response::HTTP_OK);
          $response->headers->set('Content-Type', 'text/html');
          return $response;
         */
        return new JsonResponse(array('status' => 'OK', 'message' => 'Asignación realizada con éxito.'));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/eliminar_renglones/", name="renglones_conciliacion_eliminar_renglones")
     * @Method("GET|POST")
     */
    public function eliminarRenglonesAction(Request $request) {
        $idsMovimientos = json_decode($request->request->get('ids', '[]'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        foreach ($idsMovimientos as $idMovimiento) {
            $movimiento = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion')->find($idMovimiento);


            if (!$movimiento) {
                //throw $this->createNotFoundException('No se puede encontrar el RenglonConciliacion.');
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'No se puede encontrar el RenglonConciliacion.'));
            }

            $em->remove($movimiento);
        }
        $em->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'Eliminación realizada con éxito.'));
    }

    /**
     * Obtiene los renglones de un determinado estado para la cuenta bancaria de la conciliación dada.
     * 
     */
    private function obtenerRenglones($estado, $id_conciliacion) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglones = null;

        $repository = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\RenglonConciliacion');
        $query = $repository->createQueryBuilder('r')
                ->innerJoin('r.estadoRenglonConciliacion', 'e')
                ->innerJoin('r.importacionConciliacion', 'i')
                ->innerJoin('i.conciliacion', 'c')
                ->where('e.denominacion = :estado and c.id = :id')
                ->setParameters(array('estado' => $estado, 'id' => $id_conciliacion))
                ->orderBy('r.fechaMovimientoBancario', 'ASC')
                ->getQuery();

        //var_dump($query->getSql());die;

        $renglones = $query->getResult();

        return $renglones;
    }

    /**
     * Tabla para extracto conciliado.
     *
     * @Route("/index_table_conciliado/", name="renglones_conciliacion_index_table_conciliado")
     * @Method("GET|POST")
     * 
     */
    public function indexTableConciliadoAction(Request $request) {

        $renglones = $this->obtenerRenglones(ConstanteEstadoRenglonConciliacion::ESTADO_CONCILIADO, $request->query->get('id_conciliacion'));

        return $this->render('ADIFContableBundle:ConciliacionBancaria/RenglonConciliacion:index_table.html.twig', array('entities' => $renglones));
    }

}
