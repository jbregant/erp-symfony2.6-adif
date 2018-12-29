<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia;
use ADIF\ContableBundle\Form\EgresoValor\EgresoValorGerenciaType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * EgresoValor\EgresoValorGerencia controller.
 *
 * @Route("/egresovalor_egresovalorgerencia")
 */
class EgresoValorGerenciaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Configuraci&oacute;n caja chica' => $this->generateUrl('egresovalor_egresovalorgerencia')
        );
    }

    /**
     * Lists all EgresoValor\EgresoValorGerencia entities.
     *
     * @Route("/", name="egresovalor_egresovalorgerencia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Configuraci&oacute;n caja chica'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n caja chica',
            'page_info' => 'Configuraci&oacute;n caja chica por gerencia'
        );
    }

    /**
     * Tabla para EgresoValor\EgresoValorGerencia .
     *
     * @Route("/index_table/", name="egresovalor_egresovalorgerencia_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['EgresoValor\EgresoValorGerencia'] = null;

        return $this->render('ADIFContableBundle:EgresoValor/EgresoValorGerencia:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new EgresoValor\EgresoValorGerencia entity.
     *
     * @Route("/insertar", name="egresovalor_egresovalorgerencia_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValorGerencia:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EgresoValorGerencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('egresovalor_egresovalorgerencia'));
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
            'page_title' => 'Crear EgresoValor\EgresoValorGerencia',
        );
    }

    /**
     * Creates a form to create a EgresoValor\EgresoValorGerencia entity.
     *
     * @param EgresoValorGerencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EgresoValorGerencia $entity) {
        $form = $this->createForm(new EgresoValorGerenciaType(), $entity, array(
            'action' => $this->generateUrl('egresovalor_egresovalorgerencia_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EgresoValor\EgresoValorGerencia entity.
     *
     * @Route("/crear", name="egresovalor_egresovalorgerencia_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EgresoValorGerencia();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear EgresoValor\EgresoValorGerencia'
        );
    }

    /**
     * Finds and displays a EgresoValor\EgresoValorGerencia entity.
     *
     * @Route("/{id}", name="egresovalor_egresovalorgerencia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\EgresoValorGerencia.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['EgresoValor\EgresoValorGerencia'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver EgresoValor\EgresoValorGerencia'
        );
    }

    /**
     * Displays a form to edit an existing EgresoValor\EgresoValorGerencia entity.
     *
     * @Route("/editar/{id}", name="egresovalor_egresovalorgerencia_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValorGerencia:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\EgresoValorGerencia.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar EgresoValor\EgresoValorGerencia'
        );
    }

    /**
     * Creates a form to edit a EgresoValor\EgresoValorGerencia entity.
     *
     * @param EgresoValorGerencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EgresoValorGerencia $entity) {
        $form = $this->createForm(new EgresoValorGerenciaType(), $entity, array(
            'action' => $this->generateUrl('egresovalor_egresovalorgerencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EgresoValor\EgresoValorGerencia entity.
     *
     * @Route("/actualizar/{id}", name="egresovalor_egresovalorgerencia_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EgresoValor\EgresoValorGerencia:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\EgresoValorGerencia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('egresovalor_egresovalorgerencia'));
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
            'page_title' => 'Editar EgresoValor\EgresoValorGerencia'
        );
    }

    /**
     * Deletes a EgresoValor\EgresoValorGerencia entity.
     *
     * @Route("/borrar/{id}", name="egresovalor_egresovalorgerencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\EgresoValorGerencia.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('egresovalor_egresovalorgerencia'));
    }

}
