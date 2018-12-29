<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\AnticipoSueldo;
use ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo;
use ADIF\ContableBundle\Form\AnticipoSueldoType;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * AnticipoSueldo controller.
 *
 * @Route("/anticipossueldo")
 */
class AnticipoSueldoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Anticipos de sueldo' => $this->generateUrl('anticipossueldo')
        );
    }

    /**
     * Lists all AnticipoSueldo entities.
     *
     * @Route("/", name="anticipossueldo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Anticipos de sueldo'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Anticipos de sueldo',
            'page_info' => 'Lista de anticipos de sueldo'
        );
    }

    /**
     * Tabla para AnticipoSueldo .
     *
     * @Route("/index_table/", name="anticipossueldo_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:AnticipoSueldo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Anticipos de sueldo'] = null;

        return $this->render('ADIFContableBundle:AnticipoSueldo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new AnticipoSueldo entity.
     *
     * @Route("/insertar", name="anticipossueldo_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AnticipoSueldo:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new AnticipoSueldo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);

            //Genero la novedad del anticipo
            $this->generarNovedadAnticipoSueldo($entity->getIdEmpleado(), $entity->getMonto());

            //Creo la autorizacion contable
            $this->generarAutorizacionContableAnticipoSueldo($entity);

            $em->flush();

            return $this->redirect($this->generateUrl('anticipossueldo'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear anticipo de sueldo',
        );
    }

    /**
     * Creates a form to create a AnticipoSueldo entity.
     *
     * @param AnticipoSueldo $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(AnticipoSueldo $entity) {
        $form = $this->createForm(new AnticipoSueldoType(), $entity, array(
            'action' => $this->generateUrl('anticipossueldo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AnticipoSueldo entity.
     *
     * @Route("/crear", name="anticipossueldo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new AnticipoSueldo();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear anticipo de sueldo'
        );
    }

    /**
     * Finds and displays a AnticipoSueldo entity.
     *
     * @Route("/{id}", name="anticipossueldo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoSueldo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Anticipo de sueldo'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver anticipo de sueldo'
        );
    }

    /**
     * Displays a form to edit an existing AnticipoSueldo entity.
     *
     * @Route("/editar/{id}", name="anticipossueldo_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:AnticipoSueldo:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoSueldo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar anticipo de sueldo'
        );
    }

    /**
     * Creates a form to edit a AnticipoSueldo entity.
     *
     * @param AnticipoSueldo $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(AnticipoSueldo $entity) {
        $form = $this->createForm(new AnticipoSueldoType(), $entity, array(
            'action' => $this->generateUrl('anticipossueldo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AnticipoSueldo entity.
     *
     * @Route("/actualizar/{id}", name="anticipossueldo_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:AnticipoSueldo:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:AnticipoSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoSueldo.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('anticipossueldo'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar anticipo de sueldo'
        );
    }

    /**
     * Deletes a AnticipoSueldo entity.
     *
     * @Route("/borrar/{id}", name="anticipossueldo_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:AnticipoSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoSueldo.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('anticipossueldo'));
    }

    private function generarNovedadAnticipoSueldo($idEmpleado, $monto) {
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $empleadoNovedad = new EmpleadoNovedad();
        $empleadoNovedad->setConcepto($emRRHH->getRepository('ADIFRecursosHumanosBundle:Concepto')->findOneByCodigo(Concepto::__CODIGO_ANTICIPO_SUELDO));
        $empleadoNovedad->setEmpleado($emRRHH->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado));
        $empleadoNovedad->setFechaAlta(new \DateTime());
        $empleadoNovedad->setValor($monto);
        $emRRHH->persist($empleadoNovedad);

        $emRRHH->flush();
    }

    private function generarAutorizacionContableAnticipoSueldo(AnticipoSueldo $anticipoSueldo) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $autorizacionContable = new OrdenPagoAnticipoSueldo();
        $autorizacionContable->setAnticipoSueldo($anticipoSueldo);

        $this->get('adif.orden_pago_service')->initAutorizacionContable($autorizacionContable, 'Anticipo de sueldo ' . $anticipoSueldo->getEmpleado());

        $em->persist($autorizacionContable);
    }

}
