<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Operador;
use ADIF\InventarioBundle\Form\OperadorType;

/**
 * Operador controller.
 *
 * @Route("/operador")
  */
class OperadorController extends BaseController  implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'General' => '',
            'Operadores' => $this->generateUrl('operador')
        );
    }
    /**
     * Lists all Operador entities.
     *
     * @Route("/", name="operador")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Operadores'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Operador',
            'page_info' => 'Lista de Operadores'
        );
    }

    /**
     * Tabla para Operador .
     *
     * @Route("/index_table/", name="operador_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Operador')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Operadores'] = null;

    return $this->render('ADIFInventarioBundle:Operador:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Operador entity.
     *
     * @Route("/insertar", name="operador_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Operador:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Operador();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('operador'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Operador',
        );
    }

    /**
    * Creates a form to create a Operador entity.
    *
    * @param Operador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Operador $entity)
    {
        $form = $this->createForm(new OperadorType(), $entity, array(
            'action' => $this->generateUrl('operador_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Operador entity.
     *
     * @Route("/crear", name="operador_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Operador();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Operador'
        );
}

    /**
     * Finds and displays a Operador entity.
     *
     * @Route("/{id}", name="operador_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Operador.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Operadores'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Operador'
        );
    }

    /**
     * Displays a form to edit an existing Operador entity.
     *
     * @Route("/editar/{id}", name="operador_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Operador:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Operador.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Operador'
        );
    }

    /**
    * Creates a form to edit a Operador entity.
    *
    * @param Operador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Operador $entity)
    {
        $form = $this->createForm(new OperadorType(), $entity, array(
            'action' => $this->generateUrl('operador_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Operador entity.
     *
     * @Route("/actualizar/{id}", name="operador_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Operador:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Operador.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('operador'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Operador'
        );
    }
    /**
     * Deletes a Operador entity.
     *
     * @Route("/borrar/{id}", name="operador_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        return parent::baseDeleteAction($id);

    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Divisiones
        $qbDivisiones = $em
            ->getRepository('ADIFInventarioBundle:Divisiones')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.operador = :id')
            ->setParameter('id', $id);

        $countDivisiones = $qbDivisiones->getQuery()->getSingleScalarResult();

        // Corredor
        $qbCorredor = $em
            ->getRepository('ADIFInventarioBundle:Corredor')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.operador = :id')
            ->setParameter('id', $id);

        $countCorredor = $qbCorredor->getQuery()->getSingleScalarResult();

        // CatalogoMaterialesRodantes
        $qbMaterialesRodantes = $em
            ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.idOperador = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.operador = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($countDivisiones+$countCorredor+$countMaterialesRodantes+$countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Operador '
                . 'ya que es referenciado por otras entidades.';
    }
}
