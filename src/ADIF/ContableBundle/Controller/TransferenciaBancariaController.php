<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\EstadoPagoHistorico;
use ADIF\ContableBundle\Entity\TransferenciaBancaria;
use ADIF\ContableBundle\Form\TransferenciaBancariaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * TransferenciaBancaria controller.
 *
 * @Route("/transferencia")
 */
class TransferenciaBancariaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Pagos' => $this->generateUrl('pagos_reporte_pagos')
        );
    }

    /**
     * Finds and displays a TransferenciaBancaria entity.
     *
     * @Route("/{id}", name="transferencia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TransferenciaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TransferenciaBancaria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['TransferenciaBancaria'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver transferencia bancaria'
        );
    }

    /**
     * Displays a form to edit an existing TransferenciaBancaria entity.
     *
     * @Route("/editar/{id}", name="transferencia_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:TransferenciaBancaria:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TransferenciaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TransferenciaBancaria.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar transferencia bancaria'
        );
    }

    /**
     * Creates a form to edit a TransferenciaBancaria entity.
     *
     * @param TransferenciaBancaria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TransferenciaBancaria $entity) {
        $form = $this->createForm(new TransferenciaBancariaType(), $entity, array(
            'action' => $this->generateUrl('transferencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TransferenciaBancaria entity.
     *
     * @Route("/actualizar/{id}", name="transferencia_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:TransferenciaBancaria:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TransferenciaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TransferenciaBancaria.');
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
            'page_title' => 'Editar transferencia bancaria'
        );
    }

    /**
     * Deletes a TransferenciaBancaria entity.
     *
     * @Route("/borrar/{id}", name="transferencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:TransferenciaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TransferenciaBancaria.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
    }

    /**
     * @Route("/historico_estado_pago/{id}", name="transferencia_historico_estado_pago")
     * @Method("GET")
     * @Template("ADIFContableBundle:Pago:historico_estado_pago.html.twig")
     */
    public function historicoEstadoPagoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity TransferenciaBancaria */
        $entity = $em->getRepository('ADIFContableBundle:TransferenciaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TransferenciaBancaria.');
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
     * @param TransferenciaBancaria $transferencia
     */
    private function setHistoricoEstadoPago(TransferenciaBancaria $transferencia) {

        $estadoPagoHistorico = new EstadoPagoHistorico();

        $estadoPagoHistorico->setUsuario($this->getUser());

        $estadoPagoHistorico->setTransferencia($transferencia);

        $estadoPagoHistorico->setEstadoPago($transferencia->getEstadoPago());

        $transferencia->addHistoricoEstado($estadoPagoHistorico);
    }

}
