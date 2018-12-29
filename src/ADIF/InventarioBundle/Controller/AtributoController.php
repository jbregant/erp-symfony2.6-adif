<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Atributo;
use ADIF\InventarioBundle\Form\AtributoType;

/**
 * Atributo controller.
 *
 * @Route("/atributo")
  */
class AtributoController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Atributos' => $this->generateUrl('atributo')
        );
    }
    /**
     * Lists all Atributo entities.
     *
     * @Route("/", name="atributo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Atributos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Atributo',
            'page_info' => 'Lista de Atributos'
        );
    }

    /**
     * Tabla para Atributo .
     *
     * @Route("/index_table/", name="atributo_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Atributo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Atributos'] = null;

    return $this->render('ADIFInventarioBundle:Atributo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Atributo entity.
     *
     * @Route("/insertar", name="atributo_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Atributo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Atributo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('atributo'));
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
            'page_title' => 'Crear Atributo',
        );
    }

    /**
    * Creates a form to create a Atributo entity.
    *
    * @param Atributo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Atributo $entity)
    {
        $form = $this->createForm(new AtributoType(), $entity, array(
            'action' => $this->generateUrl('atributo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Atributo entity.
     *
     * @Route("/crear", name="atributo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Atributo();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Atributo'
        );
}

    /**
     * Finds and displays a Atributo entity.
     *
     * @Route("/{id}", name="atributo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Atributo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Atributo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Atributos'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Atributo'
        );
    }

    /**
     * Displays a form to edit an existing Atributo entity.
     *
     * @Route("/editar/{id}", name="atributo_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Atributo:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Atributo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Atributo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Atributo'
        );
    }

    /**
    * Creates a form to edit a Atributo entity.
    *
    * @param Atributo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Atributo $entity)
    {
        $form = $this->createForm(new AtributoType(), $entity, array(
            'action' => $this->generateUrl('atributo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Atributo entity.
     *
     * @Route("/actualizar/{id}", name="atributo_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Atributo:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Atributo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Atributo.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('atributo'));
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
            'page_title' => 'Editar Atributo'
        );
    }
    /**
     * Deletes a Atributo entity.
     *
     * @Route("/borrar/{id}", name="atributo_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        // $em = $this->getDoctrine()->getManager($this->getEntityManager());
        // $entity = $em->getRepository('ADIFInventarioBundle:Atributo')->find($id);
        //
        // if (!$entity) {
        //     throw $this->createNotFoundException('No se puede encontrar la entidad Atributo.');
        // }
        //
        // $em->remove($entity);
        // $em->flush();
        //
        //
        // return $this->redirect($this->generateUrl('atributo'));
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
        $qbValorAtrubuto = $em
            ->getRepository('ADIFInventarioBundle:ValoresAtributo')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.atributo = :id')
            ->setParameter('id', $id);

        $counValorAtributo = $qbValorAtrubuto->getQuery()->getSingleScalarResult();

        return ($counValorAtributo) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Atributo '
                . 'ya que es referenciado por otras entidades.';
    }

}
