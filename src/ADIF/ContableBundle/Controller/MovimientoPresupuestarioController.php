<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoMovimientoPresupuestario;
use ADIF\ContableBundle\Entity\MovimientoPresupuestario;
use ADIF\ContableBundle\Form\MovimientoPresupuestarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * MovimientoPresupuestario controller.
 *
 * @Route("/movimientopresupuestario")
 */
class MovimientoPresupuestarioController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Movimientos presupuestarios' => $this->generateUrl('movimientopresupuestario')
        );
    }

    /**
     * Lists all MovimientoPresupuestario entities.
     *
     * @Route("/", name="movimientopresupuestario")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Movimientos presupuestarios'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Movimientos presupuestarios',
            'page_info' => 'Lista de movimientos presupuestarios'
        );
    }

    /**
     * Tabla para MovimientoPresupuestario.
     *
     * @Route("/index_table/", name="movimientopresupuestario_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:MovimientoPresupuestario')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Movimientos presupuestarios'] = null;

        return $this->render('ADIFContableBundle:MovimientoPresupuestario:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new MovimientoPresupuestario entity.
     *
     * @Route("/insertar", name="movimientopresupuestario_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:MovimientoPresupuestario:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new MovimientoPresupuestario();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha(new \DateTime());

        $form = $this->createCreateForm($entity, $ejercicioContable);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $tipoOperacion = $request->request->get('tipo_operacion');

            $esTransferencia = $entity->getTipoMovimientoPresupuestario()->getCodigo() == ConstanteTipoMovimientoPresupuestario::TRANSFERENCIA;

            $esAjuste = $entity->getTipoMovimientoPresupuestario()->getCodigo() == ConstanteTipoMovimientoPresupuestario::AJUSTE;

            $origen = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                    ->find($entity->getCuentaPresupuestariaOrigen()->getId());

            $origen->setMontoActual($origen->getMontoActual() + $entity->getMonto() * (($esTransferencia || $esAjuste) ? -1 : 1));

            $destino = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                    ->find($entity->getCuentaPresupuestariaDestino()->getId());

            $destino->setMontoActual($destino->getMontoActual() + ($entity->getMonto() * ($esTransferencia ? 1 : $tipoOperacion)));

            $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                    ->getEjercicioContableByFecha(new \DateTime());

            $noImputables = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                    ->findAllPresupuestariasNoImputables($ejercicioContable);

            foreach ($noImputables as $cuentaPresupuestaria) {
                $cuentaPresupuestaria->setMontoActual($cuentaPresupuestaria->actualizarMontoActual($em));
            }

            //seteo el Usuario
            $entity->setUsuario($this->getUser());

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('movimientopresupuestario'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'cuentasPresupuestarias' => $this->getCuentasPresupuestarias(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear movimiento presupuestario',
        );
    }

    /**
     * Creates a form to create a MovimientoPresupuestario entity.
     *
     * @param MovimientoPresupuestario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MovimientoPresupuestario $entity, $ejercicioContable) {
        $form = $this->createForm(new MovimientoPresupuestarioType($ejercicioContable), $entity, array(
            'action' => $this->generateUrl('movimientopresupuestario_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new MovimientoPresupuestario entity.
     *
     * @Route("/crear", name="movimientopresupuestario_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new MovimientoPresupuestario();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha(new \DateTime());

        $form = $this->createCreateForm($entity, $ejercicioContable);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'cuentasPresupuestarias' => $this->getCuentasPresupuestarias(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear movimiento presupuestario'
        );
    }

    /**
     * Finds and displays a MovimientoPresupuestario entity.
     *
     * @Route("/{id}", name="movimientopresupuestario_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoPresupuestario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Movimiento Presupuestario.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Movimiento presupuestario'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver movimiento presupuestario'
        );
    }

    /**
     * Displays a form to edit an existing MovimientoPresupuestario entity.
     *
     * @Route("/editar/{id}", name="movimientopresupuestario_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:MovimientoPresupuestario:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoPresupuestario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoPresupuestario.');
        }

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha(new \DateTime());

        $editForm = $this->createEditForm($entity, $ejercicioContable);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar movimiento presupuestario'
        );
    }

    /**
     * Creates a form to edit a MovimientoPresupuestario entity.
     *
     * @param MovimientoPresupuestario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MovimientoPresupuestario $entity, $ejercicioContable) {
        $form = $this->createForm(new MovimientoPresupuestarioType($ejercicioContable), $entity, array(
            'action' => $this->generateUrl('movimientopresupuestario_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing MovimientoPresupuestario entity.
     *
     * @Route("/actualizar/{id}", name="movimientopresupuestario_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:MovimientoPresupuestario:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:MovimientoPresupuestario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoPresupuestario.');
        }

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha(new \DateTime());

        $editForm = $this->createEditForm($entity, $ejercicioContable);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('movimientopresupuestario'));
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
            'page_title' => 'Editar movimiento presupuestario'
        );
    }

    /**
     * Deletes a MovimientoPresupuestario entity.
     *
     * @Route("/borrar/{id}", name="movimientopresupuestario_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:MovimientoPresupuestario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MovimientoPresupuestario.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('movimientopresupuestario'));
    }

    /**
     * 
     * @return type
     */
    private function getCuentasPresupuestarias() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha(new \DateTime());

        $cuentasPresupuestariasQuery = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')->findAllPresupuestariasImputables($ejercicioContable);

        $cuentasArray = [];
        $cuentasPresupuestarias = $cuentasPresupuestariasQuery->getQuery()->getResult();

        foreach ($cuentasPresupuestarias as $cuentaPresupuestaria) {
            $cuentasArray[$cuentaPresupuestaria->getId()] = array(
                'nombre' => $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->__toString(),
                'monto' => $cuentaPresupuestaria->getMontoActual(),
                'suma' => $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getSuma()
            );
        }

        return $cuentasArray;
    }

}
