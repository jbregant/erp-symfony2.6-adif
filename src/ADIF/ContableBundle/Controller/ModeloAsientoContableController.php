<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ModeloAsientoContable;
use ADIF\ContableBundle\Entity\RenglonModeloAsientoContable;
use ADIF\ContableBundle\Form\ModeloAsientoContableType;

/**
 * ModeloAsientoContable controller.
 *
 * @Route("/modelos_asiento_contable")
 */
class ModeloAsientoContableController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Modelos de asientos contables' => $this->generateUrl('modelos_asiento_contable')
        );
    }

    /**
     * Lists all ModeloAsientoContable entities.
     *
     * @Route("/", name="modelos_asiento_contable")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ModeloAsientoContable')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Modelos de asientos contables'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Modelos de asientos contables',
            'page_info' => 'Lista de modelos de asientos contables'
        );
    }

    /**
     * Creates a new ModeloAsientoContable entity.
     *
     * @Route("/insertar", name="modelos_asiento_contable_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ModeloAsientoContable:new.html.twig")
     */
    public function createAction(Request $request) {

        $modeloAsientoContable = new ModeloAsientoContable();

        $form = $this->createCreateForm($modeloAsientoContable);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $renglonesAsientoContable = $request->request->get('renglones_asiento_contable') // 
                    ? $request->request->get('renglones_asiento_contable') //
                    : array();

            $this->actualizarRenglonesModeloAsientoContable($modeloAsientoContable, $renglonesAsientoContable);

            $em->persist($modeloAsientoContable);
            $em->flush();

            return $this->redirect($this->generateUrl('modelos_asiento_contable'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $modeloAsientoContable,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear modelo de asiento contable',
        );
    }

    /**
     * Creates a form to create a ModeloAsientoContable entity.
     *
     * @param ModeloAsientoContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ModeloAsientoContable $entity) {
        $form = $this->createForm(new ModeloAsientoContableType(), $entity, array(
            'action' => $this->generateUrl('modelos_asiento_contable_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ModeloAsientoContable entity.
     *
     * @Route("/crear", name="modelos_asiento_contable_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $modeloAsientoContable = new ModeloAsientoContable();

        $form = $this->createCreateForm($modeloAsientoContable);

        $cuentasContablesImputables = $em->getRepository('ADIFContableBundle:CuentaContable')->findBy(
                array('esImputable' => true), //
                array('codigoCuentaContable' => 'ASC')
        );

        $operacionesContables = $em->getRepository('ADIFContableBundle:TipoOperacionContable')->findAll();

        $tiposMonedaMCL = $em->getRepository('ADIFContableBundle:TipoMoneda')->findBy(
                array('esMCL' => true), //
                array('denominacionTipoMoneda' => 'ASC')
        );

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $modeloAsientoContable,
            'cuentasContables' => $cuentasContablesImputables,
            'operacionesContables' => $operacionesContables,
            'tiposMoneda' => $tiposMonedaMCL,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear modelo de asiento contable'
        );
    }

    /**
     * Finds and displays a ModeloAsientoContable entity.
     *
     * @Route("/{id}", name="modelos_asiento_contable_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ModeloAsientoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ModeloAsientoContable.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver modelo de asiento contable'
        );
    }

    /**
     * Displays a form to edit an existing ModeloAsientoContable entity.
     *
     * @Route("/editar/{id}", name="modelos_asiento_contable_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ModeloAsientoContable:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ModeloAsientoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ModeloAsientoContable.');
        }

        $editForm = $this->createEditForm($entity);

        $cuentasContablesImputables = $em->getRepository('ADIFContableBundle:CuentaContable')->findBy(
                array('esImputable' => true), //
                array('codigoCuentaContable' => 'ASC')
        );

        $operacionesContables = $em->getRepository('ADIFContableBundle:TipoOperacionContable')->findAll();

        $tiposMonedaMCL = $em->getRepository('ADIFContableBundle:TipoMoneda')->findBy(
                array('esMCL' => true), //
                array('denominacionTipoMoneda' => 'ASC')
        );

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = $this->generateUrl('modelos_asiento_contable_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'cuentasContables' => $cuentasContablesImputables,
            'operacionesContables' => $operacionesContables,
            'tiposMoneda' => $tiposMonedaMCL,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar modelo de asiento contable'
        );
    }

    /**
     * Creates a form to edit a ModeloAsientoContable entity.
     *
     * @param ModeloAsientoContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ModeloAsientoContable $entity) {
        $form = $this->createForm(new ModeloAsientoContableType(), $entity, array(
            'action' => $this->generateUrl('modelos_asiento_contable_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ModeloAsientoContable entity.
     *
     * @Route("/actualizar/{id}", name="modelos_asiento_contable_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ModeloAsientoContable:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $modeloAsientoContable = $em->getRepository('ADIFContableBundle:ModeloAsientoContable')->find($id);

        if (!$modeloAsientoContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ModeloAsientoContable.');
        }

        $editForm = $this->createEditForm($modeloAsientoContable);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $renglonesAsientoContable = $request->request->get('renglones_asiento_contable') // 
                    ? $request->request->get('renglones_asiento_contable') //
                    : array();

            $this->actualizarRenglonesModeloAsientoContable($modeloAsientoContable, $renglonesAsientoContable);

            $em->flush();

            return $this->redirect($this->generateUrl('modelos_asiento_contable'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$modeloAsientoContable->__toString()] = $this->generateUrl('modelos_asiento_contable_show', array('id' => $modeloAsientoContable->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $modeloAsientoContable,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar modelo de asiento contable'
        );
    }

    /**
     * Deletes a ModeloAsientoContable entity.
     *
     * @Route("/borrar/{id}", name="modelos_asiento_contable_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ModeloAsientoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ModeloAsientoContable.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('modelos_asiento_contable'));
    }

    /**
     * 
     * @param ModeloAsientoContable $modeloAsientoContable
     * @param type $renglonesModeloAsientoContable
     */
    private function actualizarRenglonesModeloAsientoContable(ModeloAsientoContable $modeloAsientoContable, $renglonesModeloAsientoContable) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonesOriginales = $modeloAsientoContable->getRenglonesModeloAsientoContable();

        // Recorro los RenglonAsientoContable originales, eliminando aquellos 
        // que no vinieron en el Request
        foreach ($renglonesOriginales as $renglonModeloAsientoContableOriginal) {

            $existe = false;

            foreach ($renglonesModeloAsientoContable as $renglonModeloAsientoContable) {
                if ($renglonModeloAsientoContableOriginal->getId() == $renglonModeloAsientoContable['id']) {
                    $existe = true;
                }
            }
            if (!$existe) {
                $em->remove($renglonModeloAsientoContableOriginal);
            }
        }

        foreach ($renglonesModeloAsientoContable as $renglonModeloAsientoContable) {

            // Si no existe lo agrego, sino ya está en la colección
            if (!$renglonModeloAsientoContable['id']) {

                $renglonModeloAsientoContableNuevo = new RenglonModeloAsientoContable();

                $renglonModeloAsientoContableNuevo->setCuentaContable(
                        $em->getRepository('ADIFContableBundle:CuentaContable')
                                ->find($renglonModeloAsientoContable['idCuentaContable'])
                );

                $renglonModeloAsientoContableNuevo->setTipoOperacionContable(
                        $em->getRepository('ADIFContableBundle:TipoOperacionContable')
                                ->find($renglonModeloAsientoContable['idOperacionContable'])
                );

                $renglonModeloAsientoContableNuevo->setTipoMoneda(
                        $em->getRepository('ADIFContableBundle:TipoMoneda')
                                ->find($renglonModeloAsientoContable['idTipoMoneda'])
                );

                $renglonModeloAsientoContableNuevo->setImporteMO($renglonModeloAsientoContable['importeMO']);
                $renglonModeloAsientoContableNuevo->setImporteMCL($renglonModeloAsientoContable['importeMO']);

                $renglonModeloAsientoContableNuevo->setDetalle($renglonModeloAsientoContable['detalle']);

                $modeloAsientoContable->addRenglonesModeloAsientoContable($renglonModeloAsientoContableNuevo);
            }
        }
    }

    /**
     * @Route("/renglones", name="modelos_asiento_contable_renglones")
     */
    public function getRenglonesAsientoContableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idModeloAsientoContable = $request->get("id");

        $modeloAsientoContable = $em->getRepository('ADIFContableBundle:ModeloAsientoContable')
                ->find($idModeloAsientoContable);

        $renglones = [];

        foreach ($modeloAsientoContable->getRenglonesModeloAsientoContable() as $renglonModeloAsientoContable) {

            $renglones[] = array(
                'id' => $renglonModeloAsientoContable->getId(),
                'idCuentaContable' => $renglonModeloAsientoContable->getCuentaContable()->getId(),
                'cuentaContable' => $renglonModeloAsientoContable->getCuentaContable()->__toString(),
                'idOperacionContable' => $renglonModeloAsientoContable->getTipoOperacionContable()->getId(),
                'operacionContable' => $renglonModeloAsientoContable->getTipoOperacionContable()->__toString(),
                'idTipoMoneda' => $renglonModeloAsientoContable->getTipoMoneda()->getId(),
                'importeMO' => $renglonModeloAsientoContable->getImporteMO(),
                'detalle' => $renglonModeloAsientoContable->getDetalle()
            );
        }

        $response = array(
            'idConceptoAsientoContable' => $modeloAsientoContable->getConceptoAsientoContable()->getId(),
            'renglones' => $renglones,
        );

        return new JsonResponse($response);
    }

}
