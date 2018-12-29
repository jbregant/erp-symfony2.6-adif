<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\LicitacionCompra;
use ADIF\ContableBundle\Form\LicitacionCompraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * LicitacionCompra controller.
 *
 * @Route("/licitacion_compra")
 */
class LicitacionCompraController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Licitaciones de compra' => $this->generateUrl('licitacion_compra')
        );
    }

    /**
     * Lists all LicitacionCompra entities.
     *
     * @Route("/", name="licitacion_compra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Licitaciones de compra'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Licitaciones de compra',
            'page_info' => 'Lista de licitaciones de compra'
        );
    }

    /**
     * Tabla para LicitacionCompra .
     *
     * @Route("/index_table/", name="licitacion_compra_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:LicitacionCompra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Licitaciones de compra'] = null;

        return $this->render('ADIFContableBundle:LicitacionCompra:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new LicitacionCompra entity.
     *
     * @Route("/insertar", name="licitacion_compra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:LicitacionCompra:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new LicitacionCompra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('licitacion_compra'));
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
            'page_title' => 'Crear licitaci&oacute;n de compra',
        );
    }

    /**
     * Creates a form to create a LicitacionCompra entity.
     *
     * @param LicitacionCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(LicitacionCompra $entity) {
        $form = $this->createForm(new LicitacionCompraType(), $entity, array(
            'action' => $this->generateUrl('licitacion_compra_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new LicitacionCompra entity.
     *
     * @Route("/crear", name="licitacion_compra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new LicitacionCompra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear licitaci&oacute;n de compra'
        );
    }

    /**
     * Finds and displays a LicitacionCompra entity.
     *
     * @Route("/{id}", name="licitacion_compra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:LicitacionCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionCompra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Licitaci&oacute;n de compra'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver licitaci&oacute;n de compra'
        );
    }

    /**
     * Displays a form to edit an existing LicitacionCompra entity.
     *
     * @Route("/editar/{id}", name="licitacion_compra_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:LicitacionCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:LicitacionCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionCompra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar licitaci&oacute;n de compra'
        );
    }

    /**
     * Creates a form to edit a LicitacionCompra entity.
     *
     * @param LicitacionCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(LicitacionCompra $entity) {
        $form = $this->createForm(new LicitacionCompraType(), $entity, array(
            'action' => $this->generateUrl('licitacion_compra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing LicitacionCompra entity.
     *
     * @Route("/actualizar/{id}", name="licitacion_compra_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:LicitacionCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:LicitacionCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionCompra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('licitacion_compra'));
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
            'page_title' => 'Editar licitaci&oacute;n de compra'
        );
    }

    /**
     * Deletes a LicitacionCompra entity.
     *
     * @Route("/borrar/{id}", name="licitacion_compra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:LicitacionCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionCompra.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('licitacion_compra'));
    }

    /**
     * @Route("/autocomplete/form", name="autocomplete_licitacion_compra")
     */
    public function getLicitacionesCompraAction(Request $request) {

        $term = $request->query->get('term', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $licitaciones = $em->getRepository('ADIFContableBundle:LicitacionCompra')
                ->createQueryBuilder('l')
                ->where('upper(l.numero) LIKE :term')
                ->orderBy('l.numero', 'ASC')
                ->setParameter('term', '%' . strtoupper($term) . '%')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($licitaciones as $licitacion) {

            $jsonResult[] = array(
                'id' => $licitacion->getId(),
                'numero' => $licitacion->getNumero(),
                'importePliego' => $licitacion->getImportePliego()
            );
        }

        return new JsonResponse($jsonResult);
    }
    
    /**
     * @Route("/getLicitacionCompraByTipoContratacionAndNumeroAndAnio", name="get_licitacion_compra")
     */
    public function getLicitacionCompraByTipoContratacionAndNumeroAndAnioAction(Request $request) {

        $idTipoContratacion = $request->get('tipoContratacion', null);
        $numero = $request->get('numero', null);
        $anio = $request->get('anio', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $licitacion = $em->getRepository('ADIFContableBundle:LicitacionCompra')
                ->createQueryBuilder('l')
                ->where('l.numero = :numero')->setParameter('numero', $numero)
                ->andWhere('l.anio = :anio')->setParameter('anio', $anio)
                ->andWhere('l.idTipoContratacion = :idTipoContratacion')->setParameter('idTipoContratacion', $idTipoContratacion)
                ->andWhere('l.importePliego > 0')
                ->getQuery()
                ->getOneOrNullResult();

        $jsonResult = [];
        if ($licitacion != null) { 
            $jsonResult = array(
                'id' => $licitacion->getId(),
                'numero' => $licitacion->getNumero(),
                'anio' => $licitacion->getAnio(),
                'importePliego' => $licitacion->getImportePliego(),
                'tipoContratacion' => $licitacion->getTipoContratacion()->getAlias()
            );
        }
        
        return new JsonResponse($jsonResult);
    }

}
