<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Propiedades;
use ADIF\InventarioBundle\Form\PropiedadesType;

/**
 * Propiedades controller.
 *
 * @Route("/propiedades")
*/
class PropiedadesController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'General' => '',
            'Propiedades' => $this->generateUrl('propiedades')
        );
    }
    /**
     * Lists all Propiedades entities.
     *
     * @Route("/", name="propiedades")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Propiedades'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Propiedades',
            'page_info' => 'Lista de propiedades'
        );
    }

    /**
     * Tabla para Propiedades .
     *
     * @Route("/index_table/", name="propiedades_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Propiedades')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Propiedades'] = null;

    return $this->render('ADIFInventarioBundle:Propiedades:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Propiedades entity.
     *
     * @Route("/insertar", name="propiedades_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Propiedades:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Propiedades();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('propiedades'));
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
            'page_title' => 'Crear Propiedades',
        );
    }

    /**
    * Creates a form to create a Propiedades entity.
    *
    * @param Propiedades $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Propiedades $entity)
    {
        $form = $this->createForm(new PropiedadesType(), $entity, array(
            'action' => $this->generateUrl('propiedades_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Propiedades entity.
     *
     * @Route("/crear", name="propiedades_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Propiedades();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Propiedades'
        );
}

    /**
     * Finds and displays a Propiedades entity.
     *
     * @Route("/{id}", name="propiedades_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Propiedades')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Propiedades.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Propiedades'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Propiedades'
        );
    }

    /**
     * Displays a form to edit an existing Propiedades entity.
     *
     * @Route("/editar/{id}", name="propiedades_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Propiedades:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Propiedades')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Propiedades.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Propiedades'
        );
    }

    /**
    * Creates a form to edit a Propiedades entity.
    *
    * @param Propiedades $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Propiedades $entity)
    {
        $form = $this->createForm(new PropiedadesType(), $entity, array(
            'action' => $this->generateUrl('propiedades_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Propiedades entity.
     *
     * @Route("/actualizar/{id}", name="propiedades_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Propiedades:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Propiedades')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Propiedades.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('propiedades'));
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
            'page_title' => 'Editar Propiedades'
        );
    }
    /**
     * Deletes a Propiedades entity.
     *
     * @Route("/borrar/{id}", name="propiedades_delete")
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

        //PropiedadValor
        $qbValor = $em
            ->getRepository('ADIFInventarioBundle:PropiedadValor')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.idPropiedad = :id')
            ->setParameter('id', $id);

        $counValor = $qbValor->getQuery()->getSingleScalarResult();

        return ($counValor) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la Propiedad '
                . 'ya que es referenciada por otras entidades.';
    }
}
