<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Categorizacion;
use ADIF\InventarioBundle\Form\CategorizacionType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Categorizacion controller.
 *
 * @Route("/categorizacion")
  */
class CategorizacionController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Categorizaciones' => $this->generateUrl('categorizacion')
        );
    }
    /**
     * Lists all Categorizacion entities.
     *
     * @Route("/", name="categorizacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Categorizaciones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Categorizaciones',
            'page_info' => 'Lista de Categorizaciones'
        );
    }

    /**
     * Tabla para Categorizacion .
     *
     * @Route("/index_table/", name="categorizacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Categorizacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Categorizaciones'] = null;

        return $this->render('ADIFInventarioBundle:Categorizacion:index_table.html.twig', array(
            'entities' => $entities
        ));
    }
    /**
     * Creates a new Categorizacion entity.
     *
     * @Route("/insertar", name="categorizacion_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Categorizacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Categorizacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorizacion'));
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
            'page_title' => 'Crear Categorizaci&oacute;n',
        );
    }

    /**
    * Creates a form to create a Categorizacion entity.
    *
    * @param Categorizacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Categorizacion $entity)
    {
        $form = $this->createForm(new CategorizacionType(), $entity, array(
            'action' => $this->generateUrl('categorizacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Categorizacion entity.
     *
     * @Route("/crear", name="categorizacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Categorizacion();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Categorizaci&oacute;n'
        );
}

    /**
     * Finds and displays a Categorizacion entity.
     *
     * @Route("/{id}", name="categorizacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Categorizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Categorizacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Categorizaciones'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Categorizaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing Categorizacion entity.
     *
     * @Route("/editar/{id}", name="categorizacion_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Categorizacion:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Categorizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Categorizacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Categorizaci&oacute;n'
        );
    }

    /**
    * Creates a form to edit a Categorizacion entity.
    *
    * @param Categorizacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Categorizacion $entity)
    {
        $form = $this->createForm(new CategorizacionType(), $entity, array(
            'action' => $this->generateUrl('categorizacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Categorizacion entity.
     *
     * @Route("/actualizar/{id}", name="categorizacion_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Categorizacion:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Categorizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Categorizacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categorizacion'));
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
            'page_title' => 'Editar Categorizaci&oacute;n'
        );
    }
    /**
     * Deletes a Categorizacion entity.
     *
     * @Route("/borrar/{id}", name="categorizacion_delete")
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
            ->where('u.categoria = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la Categorización '
                . 'ya que es referenciado por otras entidades.';
    }

    /**
     * @Route("/lista", name="categorias_lista")
     */
    public function getCategoriasAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $ids = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Categorizacion', $this->getEntityManager());

            $queryBuilder = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion');

            foreach($ids as $key => $id){
                $entity = $em->getRepository('ADIFInventarioBundle:'.ucfirst($key))->find($id);
                $queryBuilder
                        ->andWhere("c.$key = :$key")
                        ->setParameter($key,$entity);
            }

            $query = $queryBuilder->orderBy('c.id', 'ASC')->getQuery();

            //print_r($query); die();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getDivisiones()->toArray());
        }
    }

}
