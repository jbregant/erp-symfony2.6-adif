<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Ramal;
use ADIF\InventarioBundle\Form\RamalType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ramal controller.
 *
 * @Route("/ramal")
  */
class RamalController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'General' => '',
            'Ramales' => $this->generateUrl('ramal')
        );
    }
    /**
     * Lists all Ramal entities.
     *
     * @Route("/", name="ramal")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Ramales'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Ramales',
            'page_info' => 'Lista de Ramales'
        );
    }

    /**
     * Tabla para Ramal .
     *
     * @Route("/index_table/", name="ramal_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Ramal')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Ramales'] = null;

        return $this->render('ADIFInventarioBundle:Ramal:index_table.html.twig', array(
                'entities' => $entities
        ) );
    }
    /**
     * Creates a new Ramal entity.
     *
     * @Route("/insertar", name="ramal_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Ramal:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Ramal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ramal'));
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
            'page_title' => 'Crear Ramal',
        );
    }

    /**
    * Creates a form to create a Ramal entity.
    *
    * @param Ramal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Ramal $entity)
    {
        $form = $this->createForm(new RamalType(), $entity, array(
            'action' => $this->generateUrl('ramal_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Ramal entity.
     *
     * @Route("/crear", name="ramal_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Ramal();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Ramal'
        );
}

    /**
     * Finds and displays a Ramal entity.
     *
     * @Route("/{id}", name="ramal_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Ramal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Ramal.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Ramales'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Ramal'
        );
    }

    /**
     * Displays a form to edit an existing Ramal entity.
     *
     * @Route("/editar/{id}", name="ramal_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Ramal:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Ramal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Ramal.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Ramal'
        );
    }

    /**
    * Creates a form to edit a Ramal entity.
    *
    * @param Ramal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ramal $entity)
    {
        $form = $this->createForm(new RamalType(), $entity, array(
            'action' => $this->generateUrl('ramal_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Ramal entity.
     *
     * @Route("/actualizar/{id}", name="ramal_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Ramal:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Ramal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Ramal.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ramal'));
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
            'page_title' => 'Editar Ramal'
        );
    }
    /**
     * Deletes a Ramal entity.
     *
     * @Route("/borrar/{id}", name="ramal_delete")
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
            ->where('u.ramal = :id')
            ->setParameter('id', $id);

        $countDivisiones = $qbDivisiones->getQuery()->getSingleScalarResult();

        //Estacion
        $qbEstacion = $em
            ->getRepository('ADIFInventarioBundle:Estacion')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.ramal = :id')
            ->setParameter('id', $id);

        $countEstacion = $qbEstacion->getQuery()->getSingleScalarResult();

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.ramal = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();


        return ($countDivisiones+$countEstacion+$countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Ramal '
                . 'ya que es referenciado por otras entidades.';
    }

    /**
     * @Route("/lista_ramales", name="lista_ramales")
     */
    public function getRamalesByLineaAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $idLinea = $request->request->get('id_linea');

            $linea = $em->getRepository('ADIFInventarioBundle:Linea')
                    ->find($idLinea);

            if (!$linea) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Linea.');
            }

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Ramal', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion', 'c.denominacionCorta')
                    ->where('c.linea =  :linea')
                    ->setParameter('linea', $linea)
                    ->orderBy('c.denominacionCorta', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());

        }

    }


    /**
     * @Route("/lista_por_linea", name="ramal_por_linea")
     */
    public function getRamalesByLinea2Action(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $linea = $em->getRepository('ADIFInventarioBundle:Linea')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Ramal', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion', 'c.denominacionCorta')
                    ->where('c.linea =  :linea')
                    ->setParameter('linea', $linea)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getRamales());
        }
    }

    /**
     * @Route("/lista", name="ramales_lista")
     */
    public function getRamalesAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $ids = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Ramal', $this->getEntityManager());

            $queryBuilder = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion', 'c.denominacionCorta');

            foreach($ids as $key => $id){
                $entity = $em->getRepository('ADIFInventarioBundle:'.ucfirst($key))->find($id);
                $queryBuilder
                        ->andWhere("c.$key = :$key")
                        ->setParameter($key,$entity);
            }

            $query = $queryBuilder
                ->addOrderBy('c.denominacionCorta', 'ASC')
                ->addOrderBy('c.denominacion', 'ASC')
                ->getQuery();

            return new JsonResponse($query->getResult());
        }
    }
}
