<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\PropiedadValor;
use ADIF\InventarioBundle\Form\PropiedadValorType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * PropiedadValor controller.
 *
 * @Route("/propiedadvalor")
  */
class PropiedadValorController extends BaseController
{
  private $base_breadcrumbs;
  public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
    parent::setContainer($container);
    $this->base_breadcrumbs = array(
          'Inicio' => '',
          'Inventarios' => '',
          'Configuraci&oacute;n' => '',
          'General' => '',
          'Valores por Propiedad' => $this->generateUrl('propiedadvalor')
    );
  }

  /**
  * Lists all PropiedadValor entities.
  *
  * @Route("/", name="propiedadvalor")
  * @Method("GET")
  * @Template()
  */

  public function indexAction()
  {
    $bread = $this->base_breadcrumbs;
    $bread['Valores por Propiedad'] = null;

    return array(
          'breadcrumbs' => $bread,
          'page_title' => 'Valores por Propiedad',
          'page_info' => 'Lista de Valor por Propiedad'
    );
  }

  /**
  * Tabla para PropiedadValor .
  *
  * @Route("/index_table/", name="propiedadvalor_table")
  * @Method("GET|POST")
  */
  public function indexTableAction()
  {
    $em = $this->getDoctrine()->getManager($this->getEntityManager());

    $entities = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findAll();

    $bread = $this->base_breadcrumbs;
    $bread['Valores por Propiedad'] = null;

    return $this->render('ADIFInventarioBundle:PropiedadValor:index_table.html.twig', array('entities' => $entities));
  }

  /**
  * Creates a new PropiedadValor entity.
  *
  * @Route("/insertar", name="propiedadvalor_create")
  * @Method("POST")
  * @Template("ADIFInventarioBundle:PropiedadValor:new.html.twig")
  */
  public function createAction(Request $request)
  {
    $entity = new PropiedadValor();
    $form = $this->createCreateForm($entity);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager($this->getEntityManager());
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('propiedadvalor'));
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
        'page_title' => 'Crear Valor',
    );
  }

  /**
  * Creates a form to create a PropiedadValor entity.
  *
  * @param PropiedadValor $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createCreateForm(PropiedadValor $entity)
  {
    $form = $this->createForm(new PropiedadValorType(), $entity, array(
        'action' => $this->generateUrl('propiedadvalor_create'),
        'method' => 'POST',
    ));

    $form->add('submit', 'submit', array('label' => 'Guardar'));
    return $form;
  }

  /**
  * Displays a form to create a new PropiedadValor entity.
  *
  * @Route("/crear", name="propiedadvalor_new")
  * @Method("GET")
  * @Template()
  */
  public function newAction()
  {
    $entity = new PropiedadValor();
    $form   = $this->createCreateForm($entity);

    $bread = $this->base_breadcrumbs;
    $bread['Crear'] = null;

    return array(
        'entity' => $entity,
        'form'   => $form->createView(),
        'breadcrumbs' => $bread,
        'page_title' => 'Crear Valor'
    );
  }

  /**
  * Finds and displays a PropiedadValor entity.
  *
  * @Route("/{id}", name="propiedadvalor_show")
  * @Method("GET")
  * @Template()
  */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager($this->getEntityManager());

    $entity = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('No se puede encontrar la entidad PropiedadValor.');
    }

    $bread = $this->base_breadcrumbs;
    $bread['Valores por Propiedad'] = null;

    return array(
        'entity'      => $entity,
        'breadcrumbs' => $bread,
        'page_title' => 'Ver Valor'
    );
  }

  /**
  * Displays a form to edit an existing PropiedadValor entity.
  *
  * @Route("/editar/{id}", name="propiedadvalor_edit")
  * @Method("GET")
  * @Template("ADIFInventarioBundle:PropiedadValor:new.html.twig")
  */
  public function editAction($id)
  {
    $em = $this->getDoctrine()->getManager($this->getEntityManager());

    $entity = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('No se puede encontrar la entidad PropiedadValor.');
    }

    $editForm = $this->createEditForm($entity);

    $bread = $this->base_breadcrumbs;
    $bread['Editar'] = null;

    return array(
        'entity'      => $entity,
        'form'        => $editForm->createView(),
        'breadcrumbs' => $bread,
        'page_title' => 'Editar Valor'
    );
  }

  /**
  * Creates a form to edit a PropiedadValor entity.
  *
  * @param PropiedadValor $entity The entity
  *
  * @return \Symfony\Component\Form\Form The form
  */
  private function createEditForm(PropiedadValor $entity)
  {
    $form = $this->createForm(new PropiedadValorType(), $entity, array(
        'action' => $this->generateUrl('propiedadvalor_update', array('id' => $entity->getId())),
        'method' => 'PUT',
    ));

    $form->add('submit', 'submit', array('label' => 'Guardar'));

    return $form;
  }
    /**
     * Edits an existing PropiedadValor entity.
     *
     * @Route("/actualizar/{id}", name="propiedadvalor_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:PropiedadValor:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PropiedadValor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('propiedadvalor'));
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
            'page_title' => 'Editar Valor'
        );
    }

    /**
     * Deletes a PropiedadValor entity.
     *
     * @Route("/borrar/{id}", name="propiedadvalor_delete")
     * @Method("GET")
     */
    public function deleteAction($id){
        return parent::baseDeleteAction($id);
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // PropiedadesMateriales
        $qbPropiedadesMateriales = $em
            ->getRepository('ADIFInventarioBundle:PropiedadesMateriales')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.propiedadValor = :id_prop')
            ->setParameter('id_prop', $id);

        $countPropiedadesMateriales = $qbPropiedadesMateriales->getQuery()->getSingleScalarResult();

        // ActivoLineal
        // $qbActivoLineal = $em
        //     ->getRepository('ADIFInventarioBundle:ActivoLineal')
        //     ->createQueryBuilder('u')
        //     ->select('count(u.id)')
        //     ->where('u.valoresPropiedad = :id_prop')
        //     ->setParameter('id_prop', $id);
        //
        // $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        // CatalogoMaterialesRodantes
        // $qbMaterialesRodantes = $em
        //     ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
        //     ->createQueryBuilder('u')
        //     ->select('count(u.id)')
        //     ->where('u.valoresPropiedad = :id_prop')
        //     ->setParameter('id_prop', $id);
        //
        // $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        $sql = "SELECT count(id_valor_propiedad) as count FROM activo_lineal_propiedad_valor WHERE id_valor_propiedad = ?";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $countActivoLineal = $result[0]['count'];


        $sql="SELECT count(id_valor_propiedad) as count FROM material_rodante_propiedad_valor WHERE id_valor_propiedad = ?";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $countMaterialesRodantes = $result[0]['count'];

        return ($countPropiedadesMateriales+$countActivoLineal+$countMaterialesRodantes) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Valor por Propiedad '
                . 'ya que es referenciado por otras entidades.';
    }

    /**
     * @Route("/lista_por_propiedad", name="valorpropiedad_por_propiedad")
     */
    public function getValoresPropiedadByPropiedadAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $propiedad = $em->getRepository('ADIFInventarioBundle:Propiedades')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:PropiedadValor', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.valor')
                    ->where('c.idPropiedad =  :propiedad')
                    ->setParameter('propiedad', $propiedad)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($propiedad->getValoresPropiedad()->toArray());
        }
    }

}
