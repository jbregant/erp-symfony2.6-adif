<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\ParametrosLiquidacion;
use ADIF\RecursosHumanosBundle\Form\ParametrosLiquidacionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * ParametrosLiquidacion controller.
 *
 * @Route("/parametros_liquidacion")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class ParametrosLiquidacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Parametros de liquidaci&oacute;n' => $this->generateUrl('parametros_liquidacion')
        );
    }

    /**
     * Lists all ParametrosLiquidacion entities.
     *
     * @Route("/", name="parametros_liquidacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosLiquidacion')->findAll();

        $bread = $this->base_breadcrumbs;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Parametros de liquidaci&oacute;n',
            'page_info' => 'Lista de par&aacute;metros'
        );
    }    

    /**
     * Finds and displays a ParametrosLiquidacion entity.
     *
     * @Route("/{id}", name="parametros_liquidacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ParametrosLiquidacion.');
        }

        $bread = $this->base_breadcrumbs;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Par&aacute;metro'
        );
    }

    /**
     * Displays a form to edit an existing ParametrosLiquidacion entity.
     *
     * @Route("/editar/{id}", name="parametros_liquidacion_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:ParametrosLiquidacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ParametrosLiquidacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Par&aacute;metro'
        );
    }

    /**
     * Creates a form to edit a ParametrosLiquidacion entity.
     *
     * @param ParametrosLiquidacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ParametrosLiquidacion $entity) {
        $form = $this->createForm(new ParametrosLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('parametros_liquidacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ParametrosLiquidacion entity.
     *
     * @Route("/actualizar/{id}", name="parametros_liquidacion_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:ParametrosLiquidacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ParametrosLiquidacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('parametros_liquidacion'));
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
            'page_title' => 'Editar Par&aacute;metro'
        );
    }   

}
