<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\TipoVia;
use ADIF\InventarioBundle\Form\TipoViaType;

use ADIF\BaseBundle\Controller\AlertControllerInterface;

/**
 * TipoVia controller.
 *
 * @Route("/tipovia")
  */
class TipoViaController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Tipos de V&iacute;a' => $this->generateUrl('tipovia')
        );
    }
    /**
     * Lists all TipoVia entities.
     *
     * @Route("/", name="tipovia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de V&iacute;a'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de V&iacute;a',
            'page_info' => 'Lista de Tipos de V&iacute;a'
        );
    }

    /**
     * Tabla para TipoVia .
     *
     * @Route("/index_table/", name="tipovia_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:TipoVia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de V&iacute;a'] = null;

    return $this->render('ADIFInventarioBundle:TipoVia:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TipoVia entity.
     *
     * @Route("/insertar", name="tipovia_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:TipoVia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoVia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipovia'));
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
            'page_title' => 'Crear Tipo de V&iacute;a',
        );
    }

    /**
    * Creates a form to create a TipoVia entity.
    *
    * @param TipoVia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoVia $entity)
    {
        $form = $this->createForm(new TipoViaType(), $entity, array(
            'action' => $this->generateUrl('tipovia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoVia entity.
     *
     * @Route("/crear", name="tipovia_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoVia();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Tipo de V&iacute;a'
        );
}

    /**
     * Finds and displays a TipoVia entity.
     *
     * @Route("/{id}", name="tipovia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoVia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoVia.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de V&iacute;a'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Tipo de V&iacute;a'
        );
    }

    /**
     * Displays a form to edit an existing TipoVia entity.
     *
     * @Route("/editar/{id}", name="tipovia_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:TipoVia:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoVia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoVia.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipo de V&iacute;a'
        );
    }

    /**
    * Creates a form to edit a TipoVia entity.
    *
    * @param TipoVia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoVia $entity)
    {
        $form = $this->createForm(new TipoViaType(), $entity, array(
            'action' => $this->generateUrl('tipovia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TipoVia entity.
     *
     * @Route("/actualizar/{id}", name="tipovia_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:TipoVia:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoVia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoVia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipovia'));
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
            'page_title' => 'Editar Tipo de V&iacute;a'
        );
    }
    /**
     * Deletes a TipoVia entity.
     *
     * @Route("/borrar/{id}", name="tipovia_delete")
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

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.tipoVia = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Tipo de Vía '
                . 'ya que es referenciado por otras entidades.';
    }
}
