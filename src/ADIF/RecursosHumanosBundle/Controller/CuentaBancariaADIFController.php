<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF;
use ADIF\RecursosHumanosBundle\Form\CuentaBancariaADIFType;
use ADIF\BaseBundle\Entity\EntityManagers;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CuentaBancariaADIF controller.
 *
 * @Route("/cuentas_adif")
 */
class CuentaBancariaADIFController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Cuentas bancarias' => $this->generateUrl('cuentas_adif')
        );
    }

    /**
     * Lists all CuentaBancariaADIF entities.
     *
     * @Route("/", name="cuentas_adif")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cuentas bancarias'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Cuenta bancaria',
            'page_info' => 'Lista de cuentabancariaadif'
        );
    }

    /**
     * Tabla para CuentaBancariaADIF.
     *
     * @Route("/index_table/", name="cuentas_adif_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cuentas bancarias'] = null;

        return $this->render('ADIFRecursosHumanosBundle:CuentaBancariaADIF:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new CuentaBancariaADIF entity.
     *
     * @Route("/insertar", name="cuentas_adif_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:CuentaBancariaADIF:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CuentaBancariaADIF();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentas_adif'));
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
            'page_title' => 'Crear cuenta bancaria',
        );
    }

    /**
     * Creates a form to create a CuentaBancariaADIF entity.
     *
     * @param CuentaBancariaADIF $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CuentaBancariaADIF $entity) {
        $form = $this->createForm(new CuentaBancariaADIFType(), $entity, array(
            'action' => $this->generateUrl('cuentas_adif_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CuentaBancariaADIF entity.
     *
     * @Route("/crear", name="cuentas_adif_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CuentaBancariaADIF();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cuenta bancaria'
        );
    }

    /**
     * Finds and displays a CuentaBancariaADIF entity.
     *
     * @Route("/{id}", name="cuentas_adif_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaBancariaADIF.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver cuenta bancaria'
        );
    }

    /**
     * Displays a form to edit an existing CuentaBancariaADIF entity.
     *
     * @Route("/editar/{id}", name="cuentas_adif_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:CuentaBancariaADIF:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaBancariaADIF.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta bancaria'
        );
    }

    /**
     * Creates a form to edit a CuentaBancariaADIF entity.
     *
     * @param CuentaBancariaADIF $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CuentaBancariaADIF $entity) {
        $form = $this->createForm(new CuentaBancariaADIFType(), $entity, array(
            'action' => $this->generateUrl('cuentas_adif_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing CuentaBancariaADIF entity.
     *
     * @Route("/actualizar/{id}", name="cuentas_adif_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:CuentaBancariaADIF:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaBancariaADIF.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cuentas_adif'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta bancaria'
        );
    }

    /**
     * Deletes a CuentaBancariaADIF entity.
     *
     * @Route("/borrar/{id}", name="cuentas_adif_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        // Valido que no haya Chequeras asociadas a la CuentaBancariaADIF
        $qbChequera = $emContable
                ->getRepository('ADIFContableBundle:Chequera')
                ->createQueryBuilder('c')
                ->select('count(c.id)')
                ->where('c.idCuenta = :id')
                ->setParameter('id', $id);

        $countChequeras = $qbChequera->getQuery()->getSingleScalarResult();

        // Valido que no haya TransferenciaBancarias asociadas a la CuentaBancariaADIF
        $qbTransferencia = $emContable
                ->getRepository('ADIFContableBundle:TransferenciaBancaria')
                ->createQueryBuilder('t')
                ->select('count(t.id)')
                ->where('t.idCuenta = :id')
                ->setParameter('id', $id);

        $countTransferencias = $qbTransferencia->getQuery()->getSingleScalarResult();

        return $countChequeras + $countTransferencias == 0;
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la cuenta bancaria ya que es referenciada por otras entidades.';
    }

    /**
     * Modificar ingresos pendientes
     *
     * @Route("/actualizarIngresosPendientes", name="cuentas_adif_edit_ingreso")
     * @Method("POST")   
     */
    public function actualizarIngresosPendientesAction(Request $request) {

        $id = $request->request->get('id');
        $monto = $request->request->get('monto');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $cuentaBancariaADIF CuentaBancariaADIF */
        $cuentaBancariaADIF = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id);

        if (!$cuentaBancariaADIF) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaBancariaADIF.');
        }

        $cuentaBancariaADIF->setMontoIngresosPendientes($monto);
        
        
        
        $em->flush();
        $em->clear();

        return new JsonResponse(array('result' => 'OK', 'msg' => 'Los ingresos pendientes se modificaron correctamente'));
    }

    /**
     * Modificar cheques pendientes
     *
     * @Route("/actualizarChequesPendientes", name="cuentas_adif_edit_cheque")
     * @Method("POST")   
     */
    public function actualizarChequesPendientesAction(Request $request) {

        $id = $request->request->get('id');
        $monto = $request->request->get('monto');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $cuentaBancariaADIF CuentaBancariaADIF */
        $cuentaBancariaADIF = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id);

        if (!$cuentaBancariaADIF) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaBancariaADIF.');
        }

        $cuentaBancariaADIF->setMontoChequesPendientes($monto);
        $em->flush();
        $em->clear();

        return new JsonResponse(array('result' => 'OK', 'msg' => 'Los cheques pendientes de registraci&oacute;n se modificaron correctamente'));
    }

}
