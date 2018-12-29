<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Cheque;
use ADIF\ContableBundle\Entity\EstadoPagoHistorico;
use ADIF\ContableBundle\Form\ChequeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Cheque controller.
 *
 * @Route("/cheque")
 */
class ChequeController extends BaseController {

    private $base_breadcrumbs;

    /**
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Pagos' => $this->generateUrl('pagos_reporte_pagos')
        );
    }

    /**
     * Finds and displays a Cheque entity.
     *
     * @Route("/{id}", name="cheque_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cheque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cheque.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Cheque'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Cheque'
        );
    }

    /**
     * Displays a form to edit an existing Cheque entity.
     *
     * @Route("/editar/{id}", name="cheque_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Cheque:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cheque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cheque.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Cheque'
        );
    }

    /**
     * Creates a form to edit a Cheque entity.
     *
     * @param Cheque $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cheque $entity) {
        $form = $this->createForm(new ChequeType(), $entity, array(
            'action' => $this->generateUrl('cheque_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Cheque entity.
     *
     * @Route("/actualizar/{id}", name="cheque_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Cheque:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cheque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cheque.');
        }

        $estadoPagoOriginal = $entity->getEstadoPago();

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($estadoPagoOriginal != $entity->getEstadoPago()) {

                $entity->setFechaUltimaModificacionEstado(new \DateTime());

                $this->setHistoricoEstadoPago($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
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
            'page_title' => 'Editar Cheque'
        );
    }

    /**
     * Deletes a Cheque entity.
     *
     * @Route("/borrar/{id}", name="cheque_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Cheque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cheque.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
    }

    /**
     * @Route("/historico_estado_pago/{id}", name="cheque_historico_estado_pago")
     * @Method("GET")
     * @Template("ADIFContableBundle:Pago:historico_estado_pago.html.twig")
     */
    public function historicoEstadoPagoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity Cheque */
        $entity = $em->getRepository('ADIFContableBundle:Cheque')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cheque.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Hist&oacute;rico de estados'] = null;

        return array(
            'entity' => $entity,
            'historicoEstados' => $entity->getHistoricoEstados(),
            'breadcrumbs' => $bread,
            'page_title' => 'Hist&oacute;rico de estados'
        );
    }

    /**
     * 
     * @param Cheque $cheque
     */
    private function setHistoricoEstadoPago(Cheque $cheque) {

        $estadoPagoHistorico = new EstadoPagoHistorico();

        $estadoPagoHistorico->setUsuario($this->getUser());

        $estadoPagoHistorico->setCheque($cheque);

        $estadoPagoHistorico->setEstadoPago($cheque->getEstadoPago());

        $cheque->addHistoricoEstado($estadoPagoHistorico);
    }

}
