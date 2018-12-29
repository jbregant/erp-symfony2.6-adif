<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\EjercicioContable;
use ADIF\ContableBundle\Form\EjercicioContableType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EjercicioContable controller.
 *
 * @Route("/ejercicio")
 */
class EjercicioContableController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Ejercicios' => $this->generateUrl('ejercicio')
        );
    }

    /**
     * Lists all EjercicioContable entities.
     *
     * @Route("/", name="ejercicio")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:EjercicioContable')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Ejercicios'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Ejercicio contable',
            'page_info' => 'Lista de ejercicios contables'
        );
    }

    /**
     * Creates a new EjercicioContable entity.
     *
     * @Route("/insertar", name="ejercicio_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EjercicioContable:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EjercicioContable();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $em->persist($entity);
            $em->flush();

            $this->updateEjerciciosSesionAction();

            return $this->redirect($this->generateUrl('ejercicio'));
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
            'page_title' => 'Crear ejercicio contable',
        );
    }

    /**
     * Creates a form to create a EjercicioContable entity.
     *
     * @param EjercicioContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EjercicioContable $entity) {
        $form = $this->createForm(new EjercicioContableType(), $entity, array(
            'action' => $this->generateUrl('ejercicio_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EjercicioContable entity.
     *
     * @Route("/crear", name="ejercicio_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EjercicioContable();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear ejercicio contable'
        );
    }

    /**
     * Finds and displays a EjercicioContable entity.
     *
     * @Route("/{id}", name="ejercicio_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EjercicioContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EjercicioContable.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Ejercicio contable'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver ejercicio contable'
        );
    }

    /**
     * Displays a form to edit an existing EjercicioContable entity.
     *
     * @Route("/editar/{id}", name="ejercicio_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EjercicioContable:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EjercicioContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EjercicioContable.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar ejercicio contable'
        );
    }

    /**
     * Creates a form to edit a EjercicioContable entity.
     *
     * @param EjercicioContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EjercicioContable $entity) {
        $form = $this->createForm(new EjercicioContableType(), $entity, array(
            'action' => $this->generateUrl('ejercicio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EjercicioContable entity.
     *
     * @Route("/actualizar/{id}", name="ejercicio_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EjercicioContable:new.html.twig")
     */
    public function updateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $ejercicioContable EjercicioContable */
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->find($id);

        if (!$ejercicioContable) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EjercicioContable.');
        }

        $estadoOriginal = $ejercicioContable->getEstaCerrado();

        $editForm = $this->createEditForm($ejercicioContable);
        $editForm->handleRequest($request);
        
        $mensajeErrorTransferenciaAsientosPresupuestarios = '';

        if ($editForm->isValid()) {

            // Si el ejercicio fue cerrado
            if ($ejercicioContable->getEstaCerrado() != $estadoOriginal && $ejercicioContable->getEstaCerrado()) {

                $ejercicioContable->setCantidadCierres($ejercicioContable->getCantidadCierres() + 1);

                $ejercicioActual = (int) $ejercicioContable->getDenominacionEjercicio();

                $denominacionEjercicioSiguiente = strval($ejercicioActual + 1);

                $ejercicioContableSiguiente = $em->getRepository('ADIFContableBundle:EjercicioContable')
                        ->getEjercicioContableByDenominacion($denominacionEjercicioSiguiente);

                if ($ejercicioContableSiguiente) {

//                    $mensajeErrorTransferenciaAsientosPresupuestarios = $this->get('adif.contabilidad_presupuestaria_service')
//                            ->transferirAsientosPresupuestariosEntreEjerciciosContables($ejercicioContable, $ejercicioContableSiguiente);
                } else {

                    $mensajeError = 'No se pudo realizar la transferencia de asientos presupuestarios. '
                            . 'No se encuentra configurado el ejercicio contable '
                            . $denominacionEjercicioSiguiente . '.';

                    $this->get('session')->getFlashBag()->add('error', $mensajeError);

                    $request->attributes->set('form-error', true);
                }
            }

            if ($mensajeErrorTransferenciaAsientosPresupuestarios == '') {

                $em->flush();

                $this->updateEjerciciosSesionAction();
            } else {

                $this->get('session')->getFlashBag()->add('error', $mensajeErrorTransferenciaAsientosPresupuestarios);

                $request->attributes->set('form-error', true);
            }

            return $this->redirect($this->generateUrl('ejercicio'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $ejercicioContable,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar ejercicio contable'
        );
    }

    /**
     * Deletes a EjercicioContable entity.
     *
     * @Route("/borrar/{id}", name="ejercicio_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EjercicioContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EjercicioContable.');
        }

        $em->remove($entity);
        $em->flush();

        $this->updateEjerciciosSesionAction();

        return $this->redirect($this->generateUrl('ejercicio'));
    }

    /**
     *
     * @Route("/editar_ejercicio_sesion/", name="ejercicio_editar_ejercicio_sesion")
     */
    public function updateEjercicioSesionAction(Request $request) {

        $ejercicioContableEnCurso = $request->request
                ->get('ejercicio_contable_sesion');

        $this->container->get('session')
                ->set('ejercicio_contable', $ejercicioContableEnCurso);

        return new Response();
    }

    /**
     * 
     */
    private function updateEjerciciosSesionAction() {

        $em = $this->container->get('doctrine')
                ->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmContable());

        $ejerciciosContables = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->findAll();

        $ejerciciosContablesSesion = array();

        foreach ($ejerciciosContables as $ejercicioContable) {

            /* @var $ejercicioContable EjercicioContable */

            $denominacionEjercicio = $ejercicioContable->getDenominacionEjercicio();

            $ejerciciosContablesSesion[] = $denominacionEjercicio;
        }

        $this->container->get('session')
                ->set('ejercicios_contables', $ejerciciosContablesSesion);
    }

}
