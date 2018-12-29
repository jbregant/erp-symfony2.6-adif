<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Corredor;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Form\CorredorType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Corredor controller.
 *
 * @Route("/corredor")
  */
class CorredorController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Corredores' => $this->generateUrl('corredor')
        );
    }
    /**
     * Lists all Corredor entities.
     *
     * @Route("/", name="corredor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Corredores'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Corredor',
            'page_info' => 'Lista de Corredor'
        );
    }

    /**
     * Tabla para Corredor .
     *
     * @Route("/index_table/", name="corredor_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Corredor')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Corredores'] = null;

        return $this->render('ADIFInventarioBundle:Corredor:index_table.html.twig', array(
                    'entities' => $entities
        ) );
    }
    /**
     * Creates a new Corredor entity.
     *
     * @Route("/insertar", name="corredor_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Corredor:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Corredor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('corredor'));
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
            'page_title' => 'Crear Corredor',
        );
    }

    /**
    * Creates a form to create a Corredor entity.
    *
    * @param Corredor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Corredor $entity)
    {
        $form = $this->createForm(new CorredorType(), $entity, array(
            'action' => $this->generateUrl('corredor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Corredor entity.
     *
     * @Route("/crear", name="corredor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Corredor();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Corredor'
        );
}

    /**
     * Finds and displays a Corredor entity.
     *
     * @Route("/{id}", name="corredor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Corredor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Corredor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Corredores'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Corredor'
        );
    }

    /**
     * Displays a form to edit an existing Corredor entity.
     *
     * @Route("/editar/{id}", name="corredor_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Corredor:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Corredor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Corredor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Corredor'
        );
    }

    /**
    * Creates a form to edit a Corredor entity.
    *
    * @param Corredor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Corredor $entity)
    {
        $form = $this->createForm(new CorredorType(), $entity, array(
            'action' => $this->generateUrl('corredor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Corredor entity.
     *
     * @Route("/actualizar/{id}", name="corredor_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Corredor:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Corredor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Corredor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('corredor'));
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
            'page_title' => 'Editar Corredor'
        );
    }
    /**
     * Deletes a Corredor entity.
     *
     * @Route("/borrar/{id}", name="corredor_delete")
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
            ->where('u.corredor = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Corredor '
                . 'ya que es referenciada por otras entidades.';
    }

    /**
     * @Route("/lista_por_linea", name="corredor_por_linea")
     */
    public function getCorredoresByLineaAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $linea = $em->getRepository('ADIFInventarioBundle:Linea')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Corredor', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.linea =  :linea')
                    ->setParameter('linea', $linea)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();
            //print_r($query->getResult());die();
            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getCorredores());
        }
    }

    /**
     * @Route("/lista_por_operador", name="corredor_por_operador")
     */
    public function getCorredoresByOperadorAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $operador = $em->getRepository('ADIFInventarioBundle:Operador')
                ->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Corredor', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.operador =  :operador')
                    ->setParameter('operador', $operador)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($operador->getCorredores()->toArray());
        }
    }

    /**
     * @Route("/lista_por_division", name="corredor_por_division")
     */
    public function getCorredoresByDivisionAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $division = $em->getRepository('ADIFInventarioBundle:Divisiones')
                ->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Corredor', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.division =  :division')
                    ->setParameter('division', $division)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($division->getCorredores());
        }
    }

    /**
     * @Route("/lista", name="corredores_lista")
     */
    public function getCorredoresAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $ids = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Corredor', $this->getEntityManager());

            $queryBuilder = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion');

            foreach($ids as $key => $id){
                $nombre = ($key === 'division')?'divisiones':$key;
                $entity = $em->getRepository('ADIFInventarioBundle:'.ucfirst($nombre))->find($id);
                $queryBuilder
                        ->andWhere("c.$key = :$key")
                        ->setParameter($key,$entity);
            }

            $query = $queryBuilder->orderBy('c.denominacion', 'ASC')->getQuery();

            return new JsonResponse($query->getResult());
        }
    }
}
