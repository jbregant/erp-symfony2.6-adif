<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\PlanDeCuentas;
use ADIF\ContableBundle\Form\PlanDeCuentasType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * PlanDeCuentas controller.
 *
 * @Route("/plandecuentas/configuracion")
 */
class PlanDeCuentasController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    /**
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Plan de cuentas' => $this->generateUrl('cuentacontable'),
            'Configuraci&oacute;n' => $this->generateUrl('plandecuentas_configuracion')
        );
    }

    /**
     * Lists all PlanDeCuentas entities.
     *
     * @Route("/", name="plandecuentas_configuracion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Obtengo el Plan de Cuentas
        $planDeCuentas = $em->getRepository('ADIFContableBundle:PlanDeCuentas')->
                findOneBy(array(), array('id' => 'desc'), 1, 0);

        $bread = $this->base_breadcrumbs;

        return array(
            'planDeCuentas' => $planDeCuentas,
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n del plan de cuentas'
        );
    }

    /**
     * Creates a new PlanDeCuentas entity.
     *
     * @Route("/insertar", name="plandecuentas_configuracion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:PlanDeCuentas:new.html.twig")
     */
    public function createAction(Request $request) {

        $planDeCuentas = new PlanDeCuentas();

        $form = $this->createCreateForm($planDeCuentas);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // TODO: Esto no debería ser necesario
            foreach ($planDeCuentas->getSegmentos() as $segmento) {
                $segmento->setPlanDeCuentas($planDeCuentas);
            }

            $em->persist($planDeCuentas);

            $em->flush();

            return $this->redirect($this->generateUrl('plandecuentas_configuracion'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Configurar'] = null;

        return array(
            'entity' => $planDeCuentas,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configurar plan de cuentas',
        );
    }

    /**
     * Creates a form to create a PlanDeCuentas entity.
     *
     * @param PlanDeCuentas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PlanDeCuentas $entity) {
        $form = $this->createForm(new PlanDeCuentasType(), $entity, array(
            'action' => $this->generateUrl('plandecuentas_configuracion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new PlanDeCuentas entity.
     *
     * @Route("/crear", name="plandecuentas_configuracion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new PlanDeCuentas();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Configurar'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configurar plan de cuentas'
        );
    }

    /**
     * Displays a form to edit an existing PlanDeCuentas entity.
     *
     * @Route("/editar/{id}", name="plandecuentas_configuracion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:PlanDeCuentas:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:PlanDeCuentas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PlanDeCuentas.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar configuraci&oacute;n del plan de cuentas'
        );
    }

    /**
     * Creates a form to edit a PlanDeCuentas entity.
     *
     * @param PlanDeCuentas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PlanDeCuentas $entity) {
        $form = $this->createForm(new PlanDeCuentasType(), $entity, array(
            'action' => $this->generateUrl('plandecuentas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing PlanDeCuentas entity.
     *
     * @Route("/actualizar/{id}", name="plandecuentas_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:PlanDeCuentas:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $planDeCuentas = $em->getRepository('ADIFContableBundle:PlanDeCuentas')->find($id);

        if (!$planDeCuentas) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PlanDeCuentas.');
        }

        $segmentosOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los Segmentos actuales en la BBDD
        foreach ($planDeCuentas->getSegmentos() as $segmento) {
            $segmentosOriginales->add($segmento);
        }

        $editForm = $this->createEditForm($planDeCuentas);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Por cada segmento original
            foreach ($segmentosOriginales as $segmento) {

                // Si fue eliminado
                if (false === $planDeCuentas->getSegmentos()->contains($segmento)) {

                    $planDeCuentas->removeSegmento($segmento);

                    $em->remove($segmento);
                }
            }

            // TODO: Esto no debería ser necesario
            foreach ($planDeCuentas->getSegmentos() as $segmento) {
                $segmento->setPlanDeCuentas($planDeCuentas);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('plandecuentas_configuracion'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $planDeCuentas,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar configuraci&oacute;n del plan de cuentas'
        );
    }

    /**
     * Deletes a PlanDeCuentas entity.
     *
     * @Route("/borrar/{id}", name="plandecuentas_configuracion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:PlanDeCuentas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PlanDeCuentas.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('plandecuentas_configuracion'));
    }

}
