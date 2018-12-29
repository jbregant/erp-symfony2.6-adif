<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\AdicionalCotizacion;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoComparacionCotizacion;
use ADIF\ComprasBundle\Form\AdicionalCotizacionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * AdicionalCotizacion controller.
 *
 * @Route("/adicionalcotizacion")
 */
class AdicionalCotizacionController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Adicionales de cotizaci&oacute;n' => $this->generateUrl('adicionalcotizacion')
        );
    }

    /**
     * Lists all AdicionalCotizacion entities.
     *
     * @Route("/", name="adicionalcotizacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:AdicionalCotizacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Adicionales de cotizaci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Adicionales de cotizaci&oacute;n',
            'page_info' => 'Lista de adicionales de cotizaci&oacute;n'
        );
    }

    /**
     * Creates a new AdicionalCotizacion entity.
     *
     * @Route("/insertar", name="adicionalcotizacion_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:AdicionalCotizacion:new.html.twig")
     */
    public function createAction(Request $request) {

        $entity = new AdicionalCotizacion();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Seteo el estado "Creada"
            $entity->setEstadoComparacionCotizacion(
                    $em->getRepository('ADIFComprasBundle:EstadoComparacionCotizacion')
                            ->findOneBy(
                                    array('denominacion' => ConstanteEstadoComparacionCotizacion::ESTADO_CREADA), //
                                    array('id' => 'desc'), 1, 0));

            $em->persist($entity);

            $em->flush();

            return $this->redirect($this->generateUrl('adicionalcotizacion'));
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
            'page_title' => 'Crear adicional de cotizaci&oacute;n',
        );
    }

    /**
     * Creates a form to create a AdicionalCotizacion entity.
     *
     * @param AdicionalCotizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AdicionalCotizacion $entity) {
        $form = $this->createForm(new AdicionalCotizacionType( $this->getDoctrine()->getManager($this->getEntityManager()),
                                                               $this->getDoctrine()->getManager(EntityManagers::getEmContabl())), $entity, array(
            'action' => $this->generateUrl('adicionalcotizacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AdicionalCotizacion entity.
     *
     * @Route("/crear", name="adicionalcotizacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new AdicionalCotizacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear adicional de cotizaci&oacute;n'
        );
    }

    /**
     * Finds and displays a AdicionalCotizacion entity.
     *
     * @Route("/{id}", name="adicionalcotizacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:AdicionalCotizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Adicional Cotización.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Adicional de cotizaci&oacute;n'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver adicional de cotizaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing AdicionalCotizacion entity.
     *
     * @Route("/editar/{id}", name="adicionalcotizacion_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:AdicionalCotizacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:AdicionalCotizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Adicional Cotización.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar adicional de cotizaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a AdicionalCotizacion entity.
     *
     * @param AdicionalCotizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AdicionalCotizacion $entity) {
        $form = $this->createForm(new AdicionalCotizacionType(), $entity, array(
            'action' => $this->generateUrl('adicionalcotizacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AdicionalCotizacion entity.
     *
     * @Route("/actualizar/{id}", name="adicionalcotizacion_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:AdicionalCotizacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:AdicionalCotizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Adicional Cotización.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('adicionalcotizacion'));
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
            'page_title' => 'Editar adicional de cotizaci&oacute;n'
        );
    }

    /**
     * Deletes a AdicionalCotizacion entity.
     *
     * @Route("/borrar/{id}", name="adicionalcotizacion_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
