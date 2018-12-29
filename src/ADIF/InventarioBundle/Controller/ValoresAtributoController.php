<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\ValoresAtributo;
use ADIF\InventarioBundle\Form\ValoresAtributoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * ValoresAtributo controller.
 *
 * @Route("/valoresatributo")
  */
class ValoresAtributoController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Valores de Atributos' => $this->generateUrl('valoresatributo')
        );
    }
    /**
     * Lists all ValoresAtributo entities.
     *
     * @Route("/", name="valoresatributo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Valores de Atributos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Valores de Atributos',
            'page_info' => 'Lista de valores de atributos'
        );
    }

    /**
     * Tabla para ValoresAtributo .
     *
     * @Route("/index_table/", name="valoresatributo_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Valores de Atributos'] = null;

    return $this->render('ADIFInventarioBundle:ValoresAtributo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new ValoresAtributo entity.
     *
     * @Route("/insertar", name="valoresatributo_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:ValoresAtributo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ValoresAtributo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('valoresatributo'));
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
            'page_title' => 'Crear Valores de Atributos',
        );
    }

    /**
    * Creates a form to create a ValoresAtributo entity.
    *
    * @param ValoresAtributo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ValoresAtributo $entity)
    {
        $form = $this->createForm(new ValoresAtributoType(), $entity, array(
            'action' => $this->generateUrl('valoresatributo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ValoresAtributo entity.
     *
     * @Route("/crear", name="valoresatributo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ValoresAtributo();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Valores de Atributos'
        );
}

    /**
     * Finds and displays a ValoresAtributo entity.
     *
     * @Route("/{id}", name="valoresatributo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Valores de Atributos.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Valores de Atributos'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Valores de Atributos'
        );
    }

    /**
     * Displays a form to edit an existing ValoresAtributo entity.
     *
     * @Route("/editar/{id}", name="valoresatributo_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:ValoresAtributo:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Valores de Atributos.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Valores de Atributos'
        );
    }

    /**
    * Creates a form to edit a ValoresAtributo entity.
    *
    * @param ValoresAtributo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ValoresAtributo $entity)
    {
        $form = $this->createForm(new ValoresAtributoType(), $entity, array(
            'action' => $this->generateUrl('valoresatributo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing ValoresAtributo entity.
     *
     * @Route("/actualizar/{id}", name="valoresatributo_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:ValoresAtributo:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Valores de Atributos.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('valoresatributo'));
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
            'page_title' => 'Editar Valores de Atributos'
        );
    }
    /**
     * Deletes a ValoresAtributo entity.
     *
     * @Route("/borrar/{id}", name="valoresatributo_delete")
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

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping("mappingCount");
        $rsm->addScalarResult("count", "count");
        // build rsm here
        $query = $em->createNativeQuery('SELECT COUNT(*) AS count FROM activo_lineal_atributo_valor WHERE id_valor_atributo = ?', $rsm);
        $query->setParameter(1, $id);

        $countActivoLineal = $query->getSingleScalarResult();

        return ($countActivoLineal) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Valor de Atributo '
                . 'ya que es referenciado por otras entidades.';
    }


    /**
     * @Route("/lista_por_atributo", name="valoratributo_por_atributo")
     */
    public function getValoresAtributoByAtributoAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $atributo = $em->getRepository('ADIFInventarioBundle:Atributo')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:ValoresAtributo', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.atributo =  :atributo')
                    ->setParameter('atributo', $atributo)
                    ->orderBy('c.denominacion', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($atributo->getValoresAtributo()->toArray());
        }
    }
}
