<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Facturacion\AlicuotaIngresosBrutosVenta;
use ADIF\ContableBundle\Form\Facturacion\AlicuotaIngresosBrutosVentaType;

/**
 * Facturacion\AlicuotaIngresosBrutosVenta controller.
 *
 * @Route("/alicuotasiibb")
 */
class AlicuotaIngresosBrutosVentaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Alicuotas ingresos brutos' => $this->generateUrl('alicuotasiibb')
        );
    }

    /**
     * Lists all Facturacion\AlicuotaIngresosBrutosVenta entities.
     *
     * @Route("/", name="alicuotasiibb")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Alicuota ingresos brutos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Alicuotas ingresos brutos',
            'page_info' => 'Lista de alicuotas'
        );
    }

    /**
     * Tabla para Facturacion\AlicuotaIngresosBrutosVenta .
     *
     * @Route("/index_table/", name="alicuotasiibb_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Alicuotas ingresos brutos'] = null;

        return $this->render('ADIFContableBundle:Facturacion/AlicuotaIngresosBrutosVenta:index_table.html.twig', array('entities' => $entities));
    }

    /**
     * Finds and displays a Facturacion\AlicuotaIngresosBrutosVenta entity.
     *
     * @Route("/{id}", name="alicuotasiibb_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIngresosBrutosVenta.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Alicuotas ingresos brutos'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver alicuota de ingresos brutos'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\AlicuotaIngresosBrutosVenta entity.
     *
     * @Route("/editar/{id}", name="alicuotasiibb_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIngresosBrutosVenta.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar alicuota de ingresos brutos'
        );
    }

    /**
     * Creates a form to edit a Facturacion\AlicuotaIngresosBrutosVenta entity.
     *
     * @param AlicuotaIngresosBrutosVenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AlicuotaIngresosBrutosVenta $entity) {
        $form = $this->createForm(new AlicuotaIngresosBrutosVentaType(), $entity, array(
            'action' => $this->generateUrl('alicuotasiibb_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\AlicuotaIngresosBrutosVenta entity.
     *
     * @Route("/actualizar/{id}", name="alicuotasiibb_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\AlicuotaIngresosBrutosVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AlicuotaIngresosBrutosVenta.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('alicuotasiibb'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar alicuota de ingresos brutos'
        );
    }

}
