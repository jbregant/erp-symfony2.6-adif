<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Facturacion\PuntoVenta;
use ADIF\ContableBundle\Form\Facturacion\PuntoVentaType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Punto de venta controller.
 *
 * @Route("/puntosventa")
 */
class PuntoVentaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Punto de venta' => $this->generateUrl('puntosventa')
        );
    }

    /**
     * Lists all Punto de venta entities.
     *
     * @Route("/", name="puntosventa")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_PUNTOS_VENTA')")
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Punto de venta'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Punto de venta',
            'page_info' => 'Lista de puntos de venta'
        );
    }

    /**
     * Tabla para Punto de venta .
     *
     * @Route("/index_table/", name="puntosventa_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Punto de venta'] = null;

        return $this->render('ADIFContableBundle:Facturacion/PuntoVenta:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Punto de venta entity.
     *
     * @Route("/insertar", name="puntosventa_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\PuntoVenta:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new PuntoVenta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());


            $haySuperposicion = false;

            foreach ($entity->getPuntosVentaClaseContrato() as $puntoVentaClaseContrato) {
                $puntoVentaClaseContrato->setPuntoVenta($entity);
                $ptoVentaSuperpuesto = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVentaClaseContrato')->getPuntoVentaClaseContratoSuperpuestoEnRango($puntoVentaClaseContrato->getClaseContrato(), $puntoVentaClaseContrato->getMontoMinimo(), $puntoVentaClaseContrato->getMontoMaximo());
                $haySuperposicion |= (!(empty($ptoVentaSuperpuesto)));
            }

            if ($haySuperposicion) {
                $this->get('session')->getFlashBag()->add(
                        'error', 'Hay superposición de montos.'
                );
                $request->attributes->set('form-error', true);
            } else {

                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('puntosventa'));
            }
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
            'page_title' => 'Crear punto de venta',
        );
    }

    /**
     * Creates a form to create a Punto de venta entity.
     *
     * @param PuntoVenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PuntoVenta $entity) {
        $form = $this->createForm(new PuntoVentaType(), $entity, array(
            'action' => $this->generateUrl('puntosventa_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Punto de venta entity.
     *
     * @Route("/crear", name="puntosventa_new")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_PUNTOS_VENTA')")
     */
    public function newAction() {
        $entity = new PuntoVenta();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear punto de venta'
        );
    }

    /**
     * Finds and displays a Punto de venta entity.
     *
     * @Route("/{id}", name="puntosventa_show")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_PUNTOS_VENTA')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Punto de venta.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Punto de venta'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver punto de venta'
        );
    }

    /**
     * Displays a form to edit an existing Punto de venta entity.
     *
     * @Route("/editar/{id}", name="puntosventa_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\PuntoVenta:new.html.twig")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_PUNTOS_VENTA')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Punto de venta.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar punto de venta'
        );
    }

    /**
     * Creates a form to edit a Punto de venta entity.
     *
     * @param PuntoVenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PuntoVenta $entity) {
        $form = $this->createForm(new PuntoVentaType(), $entity, array(
            'action' => $this->generateUrl('puntosventa_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Punto de venta entity.
     *
     * @Route("/actualizar/{id}", name="puntosventa_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\PuntoVenta:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Punto de venta.');
        }


        $originales = new ArrayCollection();

        foreach ($entity->getPuntosVentaClaseContrato() as $puntoVentaClaseContrato) {
            $originales->add($puntoVentaClaseContrato);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            foreach ($originales as $original) {

                // Si fue eliminado
                if (false === $entity->getPuntosVentaClaseContrato()->contains($original)) {

                    $entity->removePuntosVentaClaseContrato($original);

                    $em->remove($original);
                }
            }

            $haySuperposicion = false;

            foreach ($entity->getPuntosVentaClaseContrato() as $puntoVentaClaseContrato) {
                $puntoVentaClaseContrato->setPuntoVenta($entity);
                $ptoVentaSuperpuesto = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVentaClaseContrato')->getPuntoVentaClaseContratoSuperpuestoEnRango($puntoVentaClaseContrato->getClaseContrato(), $puntoVentaClaseContrato->getMontoMinimo(), $puntoVentaClaseContrato->getMontoMaximo(), $puntoVentaClaseContrato->getId());
                $haySuperposicion |= (!(empty($ptoVentaSuperpuesto)));
            }

            if ($haySuperposicion) {
                $this->get('session')->getFlashBag()->add(
                        'error', 'Hay superposición de montos.'
                );
                $request->attributes->set('form-error', true);
            } else {

                $em->flush();
                return $this->redirect($this->generateUrl('puntosventa'));
            }
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
            'page_title' => 'Editar punto de venta'
        );
    }

    /**
     * Deletes a Punto de venta entity.
     *
     * @Route("/borrar/{id}", name="puntosventa_delete")
     * @Method("DELETE")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_PUNTOS_VENTA')")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Punto de venta.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('puntosventa'));
    }

}
