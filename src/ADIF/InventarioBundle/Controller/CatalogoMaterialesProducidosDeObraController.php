<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra;
use ADIF\InventarioBundle\Form\CatalogoMaterialesProducidosDeObraType;
use ADIF\InventarioBundle\Entity\Inventario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * CatalogoMaterialesProducidosDeObra controller.
 *
 * @Route("/catalogomaterialesproducidosdeobra")
 * @Security("has_role('ROLE_INVENTARIO_MATERIAL_PROD_OBRA_CONSULTA')")
  */
class CatalogoMaterialesProducidosDeObraController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Datos Maestros' => '',
            'Materiales Producidos de Obra' => $this->generateUrl('catalogomaterialesproducidosdeobra')
        );
    }
    /**
     * Lists all CatalogoMaterialesProducidosDeObra entities.
     *
     * @Route("/", name="catalogomaterialesproducidosdeobra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        //$bread['Materiales Producidos de Obra'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Materiales Producidos de Obra',
            'page_info' => 'Lista de Materiales Producidos de Obra'
        );
    }

    /**
     * Tabla para CatalogoMaterialesProducidosDeObra .
     *
     * @Route("/index_table/", name="catalogomaterialesproducidosdeobra_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //$entities = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->findAll();
        $entities = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->findAllMaterialProducido();

        $bread = $this->base_breadcrumbs;
        $bread['Materiales Producidos de Obra'] = null;

        return $this->render('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra:index_table.html.twig', array(
                      'entities' => $entities
        ));
    }

    /**
     * Creates a new CatalogoMaterialesProducidosDeObra entity.
     *
     * @Route("/insertar", name="catalogomaterialesproducidosdeobra_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra:new.html.twig")
     * @Security("has_role('ROLE_INVENTARIO_MATERIAL_PROD_OBRA_MODIFICAR')")
     */
    public function createAction(Request $request)
    {
        $entity = new CatalogoMaterialesProducidosDeObra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $entity->setIdEmpresa(1);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $datos = $request->request->get('adif_inventariobundle_catalogomaterialesproducidosdeobra');

            // Estado borador
            $idEstadoInventario = 1;

            /*  Cambia ( IA-224 )
             *  Juan Kennedy de ADIF nos solicita que el campo Medidas no sea necesario para el pasaje a estado activo. Con ingresar la Unidad de Medida de Inventario bastará para pasar a estado Activo
             */
//            if($entity->getUnidadMedida() && ($entity->getMedida() != null || $entity->getVolumen() != null && $entity->getPeso() != null)){
            if( $entity->getUnidadMedida() ){
                // Si tiene Unidad de Medida, pasa a estado activo
                $idEstadoInventario = 2;
            }

            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setEstadoInventario($estadoInventario);

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            foreach ($entity->getFotos() as $foto) {
                $foto->setCatalogoMaterialesProducidosDeObra($entity);
            }
            $em->persist($entity);
            $em->flush();

            if($entity->getNumeroInterno() == null && $entity->getEstadoInventario() == "Activo"){
                $entity->setNumeroInterno($entity->getId());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('catalogomaterialesproducidosdeobra'));
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
            'page_title' => 'Crear Material Producido de Obra',
        );
    }

    /**
    * Creates a form to create a CatalogoMaterialesProducidosDeObra entity.
    *
    * @param CatalogoMaterialesProducidosDeObra $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CatalogoMaterialesProducidosDeObra $entity)
    {
        $form = $this->createForm(new CatalogoMaterialesProducidosDeObraType(), $entity, array(
            'action' => $this->generateUrl('catalogomaterialesproducidosdeobra_create'),
            'method' => 'POST',
            'where' => 'MaterialProducido' //Le indico que traiga solo las propiedades habilitadas para MaterialProducido
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CatalogoMaterialesProducidosDeObra entity.
     *
     * @Route("/crear", name="catalogomaterialesproducidosdeobra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CatalogoMaterialesProducidosDeObra();
        //$entity->setEstadoInventario(1); //Estado Borrador por default
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Material Producido de Obra'
        );
}

    /**
     * Finds and displays a CatalogoMaterialesProducidosDeObra entity.
     *
     * @Route("/{id}", name="catalogomaterialesproducidosdeobra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CatalogoMaterialesProducidosDeObra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Materiales Producidos de Obra'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Material Producido de Obra'
        );
    }

    /**
     * Displays a form to edit an existing CatalogoMaterialesProducidosDeObra entity.
     *
     * @Route("/editar/{id}", name="catalogomaterialesproducidosdeobra_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra:new.html.twig")
     * @Security("has_role('ROLE_INVENTARIO_MATERIAL_PROD_OBRA_MODIFICAR')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Material Producido de Obra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Material Producido de Obra'
        );
    }

    /**
    * Creates a form to edit a CatalogoMaterialesProducidosDeObra entity.
    *
    * @param CatalogoMaterialesProducidosDeObra $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CatalogoMaterialesProducidosDeObra $entity)
    {
        $form = $this->createForm(new CatalogoMaterialesProducidosDeObraType(), $entity, array(
            'action' => $this->generateUrl('catalogomaterialesproducidosdeobra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'where' => 'MaterialProducido' //Le indico que traiga solo las propiedades habilitadas para MaterialProducido
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing CatalogoMaterialesProducidosDeObra entity.
     *
     * @Route("/actualizar/{id}", name="catalogomaterialesproducidosdeobra_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Material Producido de Obra.');
        }

        $fotosDB = new ArrayCollection();

        // Se crea un ArrayCollection con las fotos que ya estan en la DB
        foreach ($entity->getFotos() as $adjunto) {
            $fotosDB->add($adjunto);
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF
            $datos = $request->request->get('adif_inventariobundle_catalogomaterialesproducidosdeobra');

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            // Pongo en estado borrador
            $idEstadoInventario = 1;


            /*  Cambia ( IA-224 )
             *  Juan Kennedy de ADIF nos solicita que el campo Medidas no sea necesario para el pasaje a estado activo. Con ingresar la Unidad de Medida de Inventario bastará para pasar a estado Activo
             */
//            if($entity->getUnidadMedida() && ($entity->getMedida() != null || $entity->getVolumen() != null && $entity->getPeso() != null)){
            if( $entity->getUnidadMedida() ){
                // Si tiene Unidad de Medida, pasa a estado activo
                $idEstadoInventario = 2;
            }

            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setEstadoInventario($estadoInventario);

            foreach ($entity->getFotos() as $foto) {
                $foto->setCatalogoMaterialesProducidosDeObra($entity);
            }

            // Por cada adjunto original
            foreach ($fotosDB as $foto) {

                // Si fue eliminado
                if (false === $entity->getFotos()->contains($foto)) {

                    $entity->removeFoto($foto);

                    $em->remove($foto);
                }
            }


            $em->merge($entity);
            $em->flush();

            if($entity->getNumeroInterno() == null && $entity->getEstadoInventario() == "Activo"){
                $entity->setNumeroInterno($entity->getId());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('catalogomaterialesproducidosdeobra'));
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
            'page_title' => 'Editar Material Producido de Obra'
        );
    }


    /**
     * Deletes a CatalogoMaterialesProducidosDeObra entity.
     *
     * @Route("/borrar/{id}", name="catalogomaterialesproducidosdeobra_delete")
     * @Method("GET")
     */
     public function deleteAction($id)
     {
         $em = $this->getDoctrine()->getManager($this->getEntityManager());
         $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->find($id);

         if (!$entity) {
             throw $this->createNotFoundException('No se puede encontrar la entidad CatalogoMaterialesProducidosDeObra.');
         }

         $em->remove($entity);
         $em->flush();


         return $this->redirect($this->generateUrl('catalogomaterialesproducidosdeobra'));
     }

    // public function deleteAction($id)
    // {
    //     $em = $this->getDoctrine()->getManager($this->getEntityManager());
    //     $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesProducidosDeObra')->find($id);
    //
    //     foreach ($entity->getValoresPropiedad() as $valoresPropiedad) {
    //         $entity->removeValoresPropiedad($valoresPropiedad);
    //     }
    //
    //     return parent::baseDeleteAction($id);
    //
    // }

    // public function validateLocalDeleteById($id) {
    //
    //     $em = $this->getDoctrine()->getManager($this->getEntityManager());
    //
    //     //Inventario
    //     $qbInventario = $em
    //     ->getRepository('ADIFInventarioBundle:Inventario')
    //     ->createQueryBuilder('u')
    //     ->select('count(u.id)')
    //     ->where('u.catalogoMaterialProducidoObra = :id')
    //     ->setParameter('id', $id);
    //     $countInventario = $qbInventario->getQuery()->getSingleScalarResult();
    //
    //     //Propiedad Material
    //     $qbPropiedadMaterial = $em
    //     ->getRepository('ADIFInventarioBundle:PropiedadesMateriales')
    //     ->createQueryBuilder('u')
    //     ->select('count(u.id)')
    //     ->where('u.catalogoMaterialProducidoObra = :id')
    //     ->setParameter('id', $id);
    //     $countPropiedadMaterial = $qbPropiedadMaterial->getQuery()->getSingleScalarResult();
    //
    //     return ($countInventario+$countPropiedadMaterial) == 0;
    // }
    //
    //  /**
    //   *
    //   * @return type
    //   */
    //  public function getSessionMessage() {
    //      return 'No se pudo eliminar el Material Producido de Obra '
    //              . 'ya que es referenciada por otras entidades.';
    //  }
}
