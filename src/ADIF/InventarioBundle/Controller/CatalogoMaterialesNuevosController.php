<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos;
use ADIF\InventarioBundle\Form\CatalogoMaterialesNuevosType;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CatalogoMaterialesNuevos controller.
 *
 * @Route("/catalogomaterialesnuevos")
 * @Security("has_role('ROLE_INVENTARIO_MATERIAL_NUEVO_CONSLUTA')")
  */
class CatalogoMaterialesNuevosController extends BaseController implements AlertControllerInterface
{
  private $base_breadcrumbs;

  public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
    parent::setContainer($container);
    $this->base_breadcrumbs = array(
      'Inicio' => '',
      'Inventarios' => '',
      'Datos Maestros' => '',
      'Materiales Nuevos' => $this->generateUrl('catalogomaterialesnuevos')
    );
  }


  /**
  * Lists all CatalogoMaterialesNuevos entities.
  *
  * @Route("/", name="catalogomaterialesnuevos")
  * @Method("GET")
  * @Template()
  */
  public function indexAction()
  {
    $bread = $this->base_breadcrumbs;
    $bread['Materiales Nuevos'] = null;

    return array(
      'breadcrumbs' => $bread,
      'page_title' => 'Administración de Materiales Nuevos',
      'page_info' => 'Lista de Materiales Nuevos'
    );
  }


  /**
  * Tabla para CatalogoMaterialesNuevos .
  *
  * @Route("/index_table/", name="catalogomaterialesnuevos_table")
  * @Method("GET|POST")
  */
  public function indexTableAction()
  {
    $em = $this->getDoctrine()->getManager($this->getEntityManager());
    $entities = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')->findAll();
    //$entities = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')->findAllMaterialNuevo();

    $bread = $this->base_breadcrumbs;
    $bread['Materiales Nuevos'] = null;

    return $this->render('ADIFInventarioBundle:CatalogoMaterialesNuevos:index_table.html.twig',
                  array('entities' => $entities));
  }


  /**
  * Creates a new CatalogoMaterialesNuevos entity.
  *
  * @Route("/insertar", name="catalogomaterialesnuevos_create")
  * @Method("POST")
  * @Template("ADIFInventarioBundle:CatalogoMaterialesNuevos:new.html.twig")
  * @Security("has_role('ROLE_INVENTARIO_MATERIAL_NUEVO_MODIFICAR')")
  *
  */
  public function createAction(Request $request)
  {
    $entity = new CatalogoMaterialesNuevos();
    $form = $this->createCreateForm($entity);
    $form->handleRequest($request);

    if ($form->isValid()) {

      $entity->setIdEmpresa(1); //Por default
      $entity->setValorOrigen(11);
      $entity->setValor(1);
      $entity->setRubro(1);
      $entity->setMetodoAmortizacion('metodoAmortizacion');
      $entity->setVidaUtil('vidaUtil');
      $entity->getCatalogoMaterialesNuevosCompra()->setIdEmpresa(1);

      $em = $this->getDoctrine()->getManager($this->getEntityManager());
      $datos = $request->request->get('adif_inventariobundle_catalogomaterialesnuevos');

      if(isset($datos['transporteCajas'])){$entity->setUnidadesCajas($datos['unidadesCajas']);}
      if(isset($datos['transportePallet'])){$entity->setUnidadesPallet($datos['unidadesPallet']);}

      // $compra = new \ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevosCompra();

      //$compra->setUnidadMedida(1);
      //$compra->setUnidadMedidaPackaging(1);
      //$compra->setGrupoAduana(1);
      //$compra->setItemPorUnidadCompra(1);

      //$compra->setFactor1(1);
      //$compra->setFactor2(1);
      //$compra->setFactor3(1);
      //$compra->setFactor4(1);
      //$compra->setIdEmpresa(1);

      //$datos['catalogoMaterialesNuevosCompra']['idEmpresa'] = 1; // Por default

      //if(array_key_exists('catalogoMaterialesNuevosCompra', $datos)){
      //
      //  $datosCompra['idEmpresa'] = (object) 1 ;
      //  echo "<pre>";
      //  print_r($datosCompra);
      //  echo "</pre>";
      //  die();
      //  $entity->setCatalogoMaterialesNuevosCompra($datosCompra);
      //}

      //echo "<pre>";
      //print_r($entity);
      //echo "</pre>";
      //die();

      //Asignacion del estado de inventario
      $idEstadoInventario = 1;
      if($entity->getUnidadMedida() && ($entity->getMedida() != null || $entity->getVolumen() != null || $entity->getPeso() != null)){
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

      $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find(1);
      $entity->setEstadoInventario($estadoInventario); //Borrador

      $entity->getCatalogoMaterialesNuevosCompra()->setCatalogoMaterialNuevo($entity);

      foreach ($entity->getFotos() as $foto) {
        $foto->setCatalogoMaterialesNuevos($entity);
      }

      // Guardo los datos de Catalogo Material Nuevo
      $em->persist($entity);

      // Creo un objeto Catalogo Material Nuevo Compra
      //$compra = new \ADIF\InventarioBundle\Form\CatalogoMaterialesNuevosCompraType();
      // Seteo los datos
      //$compra = $entity->getCatalogoMaterialesNuevosCompra();
      //Seteo el id
      //$compra->setCatalogoMaterialNuevo($entity->getId());
      // Guardo Catalogo Material Nuevo Compra
      //$em->persist($compra);

      $em->flush();

      // Guardo y en $entity->getId() tengo el id del registro guardado
      // $id = $entity->getId();
      //echo "ID: ".$id."<br><br>";

      //$compra->setCatalogoMaterialesNuevos($id);
      //
      //$em->persist($compra);
      //$em->flush();

      //echo "ID empresa:.".$compra->getId()."<br>";
      //echo "<br>";
      //die();

      if($entity->getNumeroInterno() == null && $entity->getEstadoInventario() == "Activo"){
        $entity->setNumeroInterno($entity->getId());
      }

      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('catalogomaterialesnuevos'));
    }
    else {
      $request->attributes->set('form-error', true);
    }

    $bread = $this->base_breadcrumbs;
    $bread['Crear'] = null;

    return array(
      'entity' => $entity,
      'form'   => $form->createView(),
      'breadcrumbs' => $bread,
      'page_title' => 'Crear Material Nuevo',
    );
  }

    /**
    * Creates a form to create a CatalogoMaterialesNuevos entity.
    *
    * @param CatalogoMaterialesNuevos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CatalogoMaterialesNuevos $entity)
    {
        $form = $this->createForm(new CatalogoMaterialesNuevosType(), $entity, array(
            'action' => $this->generateUrl('catalogomaterialesnuevos_create'),
            'method' => 'POST',
            'where' => 'MaterialNuevo'
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CatalogoMaterialesNuevos entity.
     *
     * @Route("/crear", name="catalogomaterialesnuevos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CatalogoMaterialesNuevos();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Material Nuevo'
        );
}

    /**
     * Finds and displays a CatalogoMaterialesNuevos entity.
     *
     * @Route("/{id}", name="catalogomaterialesnuevos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Material Nuevo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Administración de Materiales Nuevos'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Materiales Nuevos'
        );
    }

    /**
     * Displays a form to edit an existing CatalogoMaterialesNuevos entity.
     *
     * @Route("/editar/{id}", name="catalogomaterialesnuevos_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesNuevos:new.html.twig")
     * @Security("has_role('ROLE_INVENTARIO_MATERIAL_NUEVO_MODIFICAR')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Material Nuevo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Material Nuevo'
        );
    }

    /**
    * Creates a form to edit a CatalogoMaterialesNuevos entity.
    *
    * @param CatalogoMaterialesNuevos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CatalogoMaterialesNuevos $entity)
    {
        $form = $this->createForm(new CatalogoMaterialesNuevosType(), $entity, array(
            'action' => $this->generateUrl('catalogomaterialesnuevos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'where' => 'MaterialNuevo',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing CatalogoMaterialesNuevos entity.
     *
     * @Route("/actualizar/{id}", name="catalogomaterialesnuevos_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesNuevos:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Material Nuevo.');
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
            $entity->getCatalogoMaterialesNuevosCompra()->setCatalogoMaterialNuevo($entity);

            $datos = $request->request->get('adif_inventariobundle_catalogomaterialesnuevos');

            if(isset($datos['transporteCajas'])){$entity->setUnidadesCajas($datos['unidadesCajas']);}
            if(isset($datos['transportePallet'])){$entity->setUnidadesPallet($datos['unidadesPallet']);}

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            //Asignacion del estado de inventario
            $idEstadoInventario = 1;
            if($entity->getUnidadMedida()){
              $idEstadoInventario = 2;
            }
            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setEstadoInventario($estadoInventario);
            //

            foreach ($entity->getFotos() as $foto) {
                $foto->setCatalogoMaterialesNuevos($entity);
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

            return $this->redirect($this->generateUrl('catalogomaterialesnuevos'));
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
            'page_title' => 'Editar Material Nuevo'
        );
    }


    /**
    * Deletes a CatalogoMaterialesNuevos entity.
    *
    * @Route("/borrar/{id}", name="catalogomaterialesnuevos_delete")
    * @Method("GET")
    */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager($this->getEntityManager());
      $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesNuevos')->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('No se puede encontrar la entidad Material Nuevo.');
      }

      $em->remove($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('catalogomaterialesnuevos'));
    }


    // public function validateLocalDeleteById($id) {
    //
    //     $em = $this->getDoctrine()->getManager($this->getEntityManager());
    //
    //     //Inventario
    //     $qbInventario = $em
    //     ->getRepository('ADIFInventarioBundle:Inventario')
    //     ->createQueryBuilder('u')
    //     ->select('count(u.id)')
    //     ->where('u.catalogoMaterialNuevo = :id')
    //     ->setParameter('id', $id);
    //     $countInventario = $qbInventario->getQuery()->getSingleScalarResult();
    //
    //     //Propiedad Material
    //     $qbPropiedadMaterial = $em
    //     ->getRepository('ADIFInventarioBundle:PropiedadesMateriales')
    //     ->createQueryBuilder('u')
    //     ->select('count(u.id)')
    //     ->where('u.catalogoMaterialNuevo = :id')
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
    //      return 'No se pudo eliminar el Material Nuevo '
    //              . 'ya que es referenciada por otras entidades.';
    //  }
}
