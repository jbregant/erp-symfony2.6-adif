<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Form\LineaType;

/**
 * Linea controller.
 *
 * @Route("/linea")
  */
class LineaController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'General' => '',
            'Lineas' => $this->generateUrl('linea')
        );
    }
    /**
     * Lists all Linea entities.
     *
     * @Route("/", name="linea")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Lineas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Linea',
            'page_info' => 'Lista de Lineas'
        );
    }

    /**
     * Tabla para Linea .
     *
     * @Route("/index_table/", name="linea_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Linea')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Lineas'] = null;

    return $this->render('ADIFInventarioBundle:Linea:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Linea entity.
     *
     * @Route("/insertar", name="linea_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Linea:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Linea();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('linea'));
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
            'page_title' => 'Crear Linea',
        );
    }

    /**
    * Creates a form to create a Linea entity.
    *
    * @param Linea $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Linea $entity)
    {
        $form = $this->createForm(new LineaType(), $entity, array(
            'action' => $this->generateUrl('linea_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Linea entity.
     *
     * @Route("/crear", name="linea_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Linea();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Linea'
        );
}

    /**
     * Finds and displays a Linea entity.
     *
     * @Route("/{id}", name="linea_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Linea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Linea.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Lineas'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Linea'
        );
    }

    /**
     * Displays a form to edit an existing Linea entity.
     *
     * @Route("/editar/{id}", name="linea_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Linea:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Linea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Linea.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Linea'
        );
    }

    /**
    * Creates a form to edit a Linea entity.
    *
    * @param Linea $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Linea $entity)
    {
        $form = $this->createForm(new LineaType(), $entity, array(
            'action' => $this->generateUrl('linea_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Linea entity.
     *
     * @Route("/actualizar/{id}", name="linea_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Linea:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Linea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Linea.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('linea'));
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
            'page_title' => 'Editar Linea'
        );
    }
    /**
     * Deletes a Linea entity.
     *
     * @Route("/borrar/{id}", name="linea_delete")
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

        //Ramal
        $qbRamal = $em
            ->getRepository('ADIFInventarioBundle:Ramal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.linea = :id')
            ->setParameter('id', $id);

        $counRamal = $qbRamal->getQuery()->getSingleScalarResult();

        //Categorizacion
        /*$qbCategorizacion = $em
            ->getRepository('ADIFInventarioBundle:Categorizacion')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.linea = :id')
            ->setParameter('id', $id);

        $counCategorizacion = $qbCategorizacion->getQuery()->getSingleScalarResult();*/

        //Almacen
        $qbAlmacen = $em
            ->getRepository('ADIFInventarioBundle:Almacen')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.linea = :id')
            ->setParameter('id', $id);

        $countAlmacen = $qbAlmacen->getQuery()->getSingleScalarResult();

        //Corredor
        $qbCorredor = $em
            ->getRepository('ADIFInventarioBundle:Corredor')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.linea = :id')
            ->setParameter('id', $id);

        $countCorredor = $qbCorredor->getQuery()->getSingleScalarResult();

        // CatalogoMaterialesRodantes
        $qbMaterialesRodantes = $em
            ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.idLinea = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.linea = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($counRamal+$countAlmacen+$countCorredor+$countMaterialesRodantes+$countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la Linea '
                . 'ya que es referenciada por otras entidades.';
    }
}
