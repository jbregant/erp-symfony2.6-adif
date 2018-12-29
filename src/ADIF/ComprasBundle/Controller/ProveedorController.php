<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\CertificadoExencion;
use ADIF\ComprasBundle\Entity\ClienteProveedor;
use ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\ComprasBundle\Entity\Padron;
use ADIF\ComprasBundle\Entity\EstadoPadron;
use ADIF\ComprasBundle\Entity\RenglonPadron;
use ADIF\ComprasBundle\Form\ProveedorType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPadron;
use ADIF\ContableBundle\Entity\TipoImpuesto;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ZipArchive;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\Expr\Join;
use DateTime;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * Proveedor controller.
 *
 * @Route("/proveedor")
 */
class ProveedorController extends BaseController implements AlertControllerInterface {

    /**
     * RF_COLUMN_INDEX_CUIT
     */
    const RF_COLUMN_INDEX_CUIT = 3;

    /**
     * MS_COLUMN_INDEX_CUIT
     */
    const MS_COLUMN_INDEX_CUIT = 3;

    /**
     * MS_COLUMN_INDEX_ALICUOTA_PERCEPCION
     */
    const MS_COLUMN_INDEX_ALICUOTA_PERCEPCION = 7;

    /**
     * MS_COLUMN_INDEX_ALICUOTA_RETENCION
     */
    const MS_COLUMN_INDEX_ALICUOTA_RETENCION = 8;

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Proveedores' => $this->generateUrl('proveedor')
        );
    }

    /**
     * Lists all Proveedor entities.
     *
     * @Route("/", name="proveedor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = null;

        return array(
            'estados_proveedor' => $this->listaEstadoProveedorAction(),
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor',
            'page_info' => 'Lista de proveedores'
        );
    }
    
    /**
     * Lists all Proveedor entities.
     *
     * @Route("/extendido", name="proveedor_extendido")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:index_extendido.html.twig")
     */
    public function indexExtendidoAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = null;

        return array(
            'estados_proveedor' => $this->listaEstadoProveedorAction(),
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor',
            'page_info' => 'Lista de proveedores'
        );
    }

    /**
     * Creates a new Proveedor entity.
     *
     * @Route("/insertar", name="proveedor_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Proveedor:new.html.twig")
     */
    public function createAction(Request $request) {

        $proveedor = new Proveedor();

        $form = $this->createCreateForm($proveedor);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Actualiza el Convenio Multilateral
            $this->updateConvenioMultilateral($em, $proveedor->getClienteProveedor());

            // Actualiza los Certificados de ExenciÃ³n
            $this->updateCertificadosExencion($em, $proveedor);

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($proveedor->getClienteProveedor());

            // Si la Cuenta NO fue seteada
            if ($proveedor->getCuenta() == null || $proveedor->getCuenta()->getCbu() == null) {
                $proveedor->setCuenta(null);
            }

            // A cada ContactoProveedor, le seteo el Proveedor
            foreach ($proveedor->getContactosProveedor() as $contactoProveedor) {
                $contactoProveedor->setProveedor($proveedor);
            }

            // A cada CAI, le seteo el Proveedor
            foreach ($proveedor->getCais() as $cai) {
                $cai->setProveedor($proveedor);
            }

            // A cada ProveedorUTE, le seteo el Proveedor
            foreach ($proveedor->getProveedoresUTE() as $proveedorUTE) {
                $proveedorUTE->setProveedorUTE($proveedor);
            }

            // A la EvaluacionProveedor, le seteo el Proveedor
            $proveedor->getEvaluacionProveedor()->setProveedor($proveedor);

            // A cada EvaluacionAspectoProveedor, le seteo la EvaluacionProveedor
            foreach ($proveedor->getEvaluacionProveedor()->getEvaluacionesAspectos() as $evaluacionAspecto) {
                $evaluacionAspecto->setEvaluacionProveedor($proveedor->getEvaluacionProveedor());
            }
			
            if ($proveedor->getClienteProveedor()->getEsExtranjero()) {
                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                        ->findOneBy(
                        array('codigoIdentificacion' => $proveedor->getClienteProveedor()
                            ->getCodigoIdentificacion())
                );
            } else {
                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                        ->findOneBy(array('CUIT' => $proveedor->getClienteProveedor()->getCuit()));
            }
			
			$codigo = $this->get('adif.cliente_proveedor_service')->getSiguienteCodigoClienteProveedor();
            if ($clienteProveedor != null 
				&& $clienteProveedor->getProveedores() != null
				&& isset($clienteProveedor->getProveedores()[0])
				&& $clienteProveedor->getProveedores()[0]->getEstadoProveedor()->getId() != 2) {
				// Le voy a setear los datos del proveedor, si no esta en estado inactivo
				
				// Seteo el codigo del clienteProveedor
				$clienteProveedor->setCodigo($codigo);
                $proveedor->setClienteProveedor($clienteProveedor);
            } else {
				$proveedor->getClienteProveedor()->setCodigo($codigo);
			}
			
            // Creo/actualizo historicos de condicion fiscal
            $this->chequearHistoricoCondicionFiscal($em, $proveedor);

            $proveedor->setMontoFacturadoAcumulado(0);
            $proveedor->setMontoSUSS(0);

            $em->persist($proveedor);
            $em->flush();

            return $this->redirect($this->generateUrl('proveedor'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $proveedor,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear proveedor',
        );
    }

    /**
     * Creates a form to create a Proveedor entity.
     *
     * @param Proveedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Proveedor $entity) {
        $form = $this->createForm(new ProveedorType(), $entity, array(
            'action' => $this->generateUrl('proveedor_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Proveedor entity.
     *
     * @Route("/crear", name="proveedor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $parametroBusqueda = $request->query->get('esExtranjero') ? 'codigoIdentificacion' : 'CUIT';

        $clienteProveedor = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')
                ->findOneBy(array($parametroBusqueda => $request->query->get('identificacion')));

        // Nuevo
        if (!$clienteProveedor) {

            $clienteProveedorService = $this->get('adif.cliente_proveedor_service');

            $proveedor = new Proveedor();

            $clienteProveedor = new ClienteProveedor();

            $clienteProveedor->setCodigo($clienteProveedorService->getSiguienteCodigoClienteProveedor());

            if ($request->query->get('esExtranjero')) {

                $clienteProveedor->setCodigoIdentificacion($request->query->get('identificacion'));

                $clienteProveedor->setEsExtranjero(true);

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::PROVEEDOR_EXTRANJERO);

                $proveedor->setCuentaContable($cuentaContable);
            } else {
                $clienteProveedor->setCUIT($request->query->get('identificacion'));

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::PROVEEDOR_NACIONAL);

                $proveedor->setCuentaContable($cuentaContable);
            }
            $proveedor->setClienteProveedor($clienteProveedor);
        } else {

            $proveedorExiste = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                    ->findOneByClienteProveedor($clienteProveedor);
					
            if (!$proveedorExiste) {
				// Si no existe el proveedor
                $proveedor = new Proveedor();

                $proveedor->setClienteProveedor($clienteProveedor);
            } else {
                // Ya existe
				if ($proveedorExiste->getEstadoProveedor()->getId() != 2) {
					// Si no es inactivo, envio el mensaje de error
					$codigo = $request->query->get('esExtranjero') ? 'CDI' : 'CUIT';

					$this->get('session')->getFlashBag()->add(
							'warning', 'Ya existe un proveedor con el n&uacute;mero de ' . $codigo . ' ingresado.'
					);

					return $this->redirect($this->generateUrl('proveedor'));
				} else {
					// Si ya existe, pero el proveedor esta inactivo
					$proveedor = new Proveedor();
					//$clienteProveedor = new ClienteProveedor();
					$clienteProveedor->setCUIT($request->query->get('identificacion'));
					$proveedor->setClienteProveedor($clienteProveedor);
				}
            }
        }

        // Get EvaluacionProveedor
        $evaluacionProveedor = $proveedor->getEvaluacionProveedor();

        // Obtengo los AspectoEvaluacion cargados en la BBDD
        $aspectosEvaluacion = $emCompras->getRepository('ADIFComprasBundle:AspectoEvaluacion')->findAll();

        // Por cada AspectoEvaluacion, creo un EvaluacionAspectoProveedor
        foreach ($aspectosEvaluacion as $aspectoEvaluacion) {

            $evaluacionAspectoProveedor = new EvaluacionAspectoProveedor();

            $evaluacionAspectoProveedor->setEvaluacionProveedor($evaluacionProveedor);
            $evaluacionAspectoProveedor->setAspectoEvaluacion($aspectoEvaluacion);

            $evaluacionProveedor->addEvaluacionesAspecto($evaluacionAspectoProveedor);
        }

        // Obtengo el TipoMoneda de curso legal
        $tipoMonedaMCL = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->
                findOneBy(array('esMCL' => true), array('id' => 'desc'), 1, 0);

        $proveedor->setTipoMoneda($tipoMonedaMCL);

        // Obtengo el EstadoProveedor cuya denominacion sea igual a "Activo"
        $estadoProveedorActivo = $emCompras->getRepository('ADIFComprasBundle:EstadoProveedor')->
                findOneBy(array('denominacionEstadoProveedor' => 'Activo'), array('id' => 'desc'), 1, 0);

        $proveedor->setEstadoProveedor($estadoProveedorActivo);

        $form = $this->createCreateForm($proveedor);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $proveedor,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear proveedor'
        );
    }

    /**
     * Finds and displays a Proveedor entity.
     *
     * @Route("/{id}", name="proveedor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Proveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getClienteProveedor()->getRazonSocial()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver proveedor'
        );
    }

    /**
     * Displays a form to edit an existing Proveedor entity.
     *
     * @Route("/editar/{id}", name="proveedor_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($id);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $evaluacionProveedor = $proveedor->getEvaluacionProveedor();

// Obtengo los AspectoEvaluacion nuevos que no estÃ¡n asignados a la Evaluacion del Proveedor
        $aspectosEvaluacion = $em->getRepository('ADIFComprasBundle:AspectoEvaluacion')
                ->getNuevosAspectosByEvaluacionProveedor($evaluacionProveedor->getId());

// Por cada AspectoEvaluacion obtenido, creo un EvaluacionAspectoProveedor
        foreach ($aspectosEvaluacion as $aspectoEvaluacion) {

            $evaluacionAspectoProveedor = new EvaluacionAspectoProveedor();

            $evaluacionAspectoProveedor->setEvaluacionProveedor($evaluacionProveedor);
            $evaluacionAspectoProveedor->setAspectoEvaluacion($aspectoEvaluacion);

            $evaluacionProveedor->addEvaluacionesAspecto($evaluacionAspectoProveedor);
        }

        $em->persist($evaluacionProveedor);
        $em->flush();

        $editForm = $this->createEditForm($proveedor);

        $bread = $this->base_breadcrumbs;
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', array('id' => $proveedor->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $proveedor,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar proveedor'
        );
    }

    /**
     * Creates a form to edit a Proveedor entity.
     *
     * @param Proveedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Proveedor $entity) {
        $form = $this->createForm(new ProveedorType(true), $entity, array(
            'action' => $this->generateUrl('proveedor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Proveedor entity.
     *
     * @Route("/actualizar/{id}", name="proveedor_update")
     * @Method({"GET", "PUT"})
     * @Template("ADIFComprasBundle:Proveedor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $proveedor Proveedor */
        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($id);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $proveedor->setFechaUltimaActualizacion(new \DateTime());

        $adjuntosOriginales = new ArrayCollection();

        $datosContactoOriginales = new ArrayCollection();

        $contactosProveedorOriginales = new ArrayCollection();

        $caisOriginales = new ArrayCollection();

        $proveedoresUTEOriginales = new ArrayCollection();

// Creo un ArrayCollection de los adjuntos actuales en la BBDD
        foreach ($proveedor->getClienteProveedor()->getArchivos() as $adjunto) {
            $adjuntosOriginales->add($adjunto);
        }

// Creo un ArrayCollection de los DatoContacto actuales en la BBDD
        foreach ($proveedor->getClienteProveedor()->getDatosContacto() as $datoContacto) {
            $datosContactoOriginales->add($datoContacto);
        }

// Creo un ArrayCollection de los ContactoProveedor actuales en la BBDD
        foreach ($proveedor->getContactosProveedor() as $contactoProveedor) {
            $contactosProveedorOriginales->add($contactoProveedor);
        }

// Creo un ArrayCollection de los CAIs actuales en la BBDD
        foreach ($proveedor->getCais() as $cai) {
            $caisOriginales->add($cai);
        }

// Creo un ArrayCollection de los ProveedorUTEs actuales en la BBDD
        foreach ($proveedor->getProveedoresUTE() as $proveedorUTE) {
            $proveedoresUTEOriginales->add($proveedorUTE);
        }

        if ($proveedor->getClienteProveedor()->getEsExtranjero()) {
            $codigoAnterior = $proveedor->getClienteProveedor()->getCodigoIdentificacion();
        } else {
            $codigoAnterior = $proveedor->getClienteProveedor()->getCUIT();
        }

        $editForm = $this->createEditForm($proveedor);
        $editForm->handleRequest($request);

        $verificarCodigo = false;

// Verifico si tengo que validar si el CUIT o CDI esta en uso
        if ($proveedor->getClienteProveedor()->getEsExtranjero()) {
            if ($codigoAnterior != $proveedor->getClienteProveedor()->getCodigoIdentificacion()) {
                $parametroBusqueda = 'codigoIdentificacion';
                $valorBusqueda = $proveedor->getClienteProveedor()->getCodigoIdentificacion();
                $verificarCodigo = true;
            }
        } else {
            if ($codigoAnterior != $proveedor->getClienteProveedor()->getCUIT()) {
                $parametroBusqueda = 'CUIT';
                $valorBusqueda = $proveedor->getClienteProveedor()->getCUIT();
                $verificarCodigo = true;
            }
        }

        if ($verificarCodigo) {

// Verifico si el CUIT o CDI esta en uso
            $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                    ->findOneBy(array($parametroBusqueda => $valorBusqueda));

            if ($clienteProveedor) {
                $editForm->addError(new FormError('El nÃºmero de ' . $parametroBusqueda . ' proporcionado pertenece a otro proveedor'));
            }
        }

        if ($editForm->isValid()) {

            $proveedor->setFechaUltimaActualizacion(new \DateTime());

// Actualiza el Convenio Multilateral
            $this->updateConvenioMultilateral($em, $proveedor->getClienteProveedor());

// Actualiza los Certificados de ExenciÃ³n
            $this->updateCertificadosExencion($em, $proveedor);

// Actualiza los archivos adjuntos
            $this->updateAdjuntos($proveedor->getClienteProveedor());

// Si la Cuenta NO fue seteada
            if ($proveedor->getCuenta() == null || $proveedor->getCuenta()->getCbu() == null) {
                $proveedor->setCuenta(null);
            }

// A cada ContactoProveedor, le seteo el Proveedor
            foreach ($proveedor->getContactosProveedor() as $contactoProveedor) {
                $contactoProveedor->setProveedor($proveedor);
            }

// A cada CAI, le seteo el Proveedor
            foreach ($proveedor->getCais() as $cai) {
                $cai->setProveedor($proveedor);
            }

// A cada ProveedorUTE, le seteo el Proveedor
            foreach ($proveedor->getProveedoresUTE() as $proveedorUTE) {
                $proveedorUTE->setProveedorUTE($proveedor);
            }

// Por cada adjunto original
            foreach ($adjuntosOriginales as $adjunto) {

// Si fue eliminado
                if (false === $proveedor->getClienteProveedor()->getArchivos()->contains($adjunto)) {

                    $proveedor->getClienteProveedor()->removeArchivo($adjunto);

                    $em->remove($adjunto);
                }
            }

// Por cada datoContacto original
            foreach ($datosContactoOriginales as $datoContacto) {

// Si fue eliminado
                if (false === $proveedor->getClienteProveedor()->getDatosContacto()->contains($datoContacto)) {

                    $proveedor->getClienteProveedor()->removeDatosContacto($datoContacto);

                    $em->remove($datoContacto);
                }
            }

// Por cada contactoProveedor original
            foreach ($contactosProveedorOriginales as $contactoProveedor) {

// Si fue eliminado
                if (false === $proveedor->getContactosProveedor()->contains($contactoProveedor)) {

                    $proveedor->removeContactosProveedor($contactoProveedor);

                    $contactoProveedor->getDatosContacto()->clear();

                    $em->remove($contactoProveedor);
                }
            }

// Por cada cai original
            foreach ($caisOriginales as $cai) {

// Si fue eliminado
                if (false === $proveedor->getCais()->contains($cai)) {

                    $proveedor->removeCai($cai);

                    $em->remove($cai);
                }
            }

// Por cada ProveedorUTE original
            foreach ($proveedoresUTEOriginales as $proveedorUTE) {

// Si fue eliminado
                if (false === $proveedor->getProveedoresUTE()->contains($proveedorUTE)) {

                    $proveedor->removeProveedoresUTE($proveedorUTE);

                    $em->remove($proveedorUTE);
                }
            }

// A la EvaluacionProveedor, le seteo el Proveedor
            $proveedor->getEvaluacionProveedor()->setProveedor($proveedor);

// Creo/actualizo histÃ³ricos de condiciÃ³n fiscal
            $this->chequearHistoricoCondicionFiscal($em, $proveedor);

            $em->flush();

            return $this->redirect($this->generateUrl('proveedor'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', array('id' => $proveedor->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $proveedor,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar proveedor'
        );
    }

    /**
     * Deletes a Proveedor entity.
     *
     * @Route("/borrar/{id}", name="proveedor_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:Proveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('proveedor'));
    }

    /**
     * @Route("/estados", name="proveedor_estados")
     */
    public function listaEstadoProveedorAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:EstadoProveedor', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstadoProveedor')
                ->orderBy('e.denominacionEstadoProveedor', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'proveedor_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * 
     * @param type $em
     * @param Proveedor $proveedor
     */
    private function updateCertificadosExencion($em, Proveedor $proveedor) {

        if (null != $proveedor->getCertificadoExencionIVA()) {

            if (null == $proveedor->getCertificadoExencionIVA()->getNumeroCertificado()) {
                $em->remove($proveedor->getCertificadoExencionIVA());
                $proveedor->setCertificadoExencionIVA(null);
            } else {
                $this->setAdjuntoACertificadoExencion($proveedor->getCertificadoExencionIVA());
            }
        }

        if (null != $proveedor->getCertificadoExencionGanancias()) {

            if (null == $proveedor->getCertificadoExencionGanancias()->getNumeroCertificado()) {
                $em->remove($proveedor->getCertificadoExencionGanancias());
                $proveedor->setCertificadoExencionGanancias(null);
            } else {
                $this->setAdjuntoACertificadoExencion($proveedor->getCertificadoExencionGanancias());
            }
        }

        if (null != $proveedor->getCertificadoExencionIngresosBrutos()) {

            if (null == $proveedor->getCertificadoExencionIngresosBrutos()->getNumeroCertificado()) {
                $em->remove($proveedor->getCertificadoExencionIngresosBrutos());
                $proveedor->setCertificadoExencionIngresosBrutos(null);
            } else {
                $this->setAdjuntoACertificadoExencion($proveedor->getCertificadoExencionIngresosBrutos());
            }
        }

        if (null != $proveedor->getCertificadoExencionSUSS()) {

            if (null == $proveedor->getCertificadoExencionSUSS()->getNumeroCertificado()) {
                $em->remove($proveedor->getCertificadoExencionSUSS());
                $proveedor->setCertificadoExencionSUSS(null);
            } else {
                $this->setAdjuntoACertificadoExencion($proveedor->getCertificadoExencionSUSS());
            }
        }
    }

    /**
     * 
     * @param type $em
     * @param \ADIF\ComprasBundle\Controller\ClienteProveedor $clienteProveedor
     */
    private function updateConvenioMultilateral($em, ClienteProveedor $clienteProveedor) {
        if (!$clienteProveedor->getDatosImpositivos()->getCondicionIngresosBrutos() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
            if (null != $clienteProveedor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos()) {
                $em->remove($clienteProveedor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos());
                $clienteProveedor->getDatosImpositivos()->setConvenioMultilateralIngresosBrutos(null);
            }
        } else {
            if (null != $clienteProveedor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos()) {
                $clienteProveedor->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos()->setDatosImpositivos($clienteProveedor->getDatosImpositivos());
            }
        }
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Controller\CertificadoExencion $certificadoExencion
     */
    private function setAdjuntoACertificadoExencion(CertificadoExencion $certificadoExencion) {

        if (null != $certificadoExencion->getAdjunto() && //
                null != $certificadoExencion->getAdjunto()->getArchivo()) {

            $certificadoExencion->getAdjunto()
                    ->setNombre($certificadoExencion->getAdjunto()->getArchivo()->getClientOriginalName());

            $certificadoExencion->getAdjunto()->setCertificadoExencion($certificadoExencion);
        }
    }

    /**
     * 
     * @param ClienteProveedor $clienteProveedor
     */
    private function updateAdjuntos(ClienteProveedor $clienteProveedor) {

        foreach ($clienteProveedor->getArchivos() as $adjunto) {

            if ($adjunto != null && $adjunto->getArchivo() != null) {

                $adjunto->setClienteProveedor($clienteProveedor);

                $adjunto->setNombre($adjunto->getArchivo()->getClientOriginalName());
            }
        }
    }

    /**
     * Tabla para Proveedor.
     *
     * @Route("/index_table/", name="proveedor_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('cuitDni', 'cuitDni');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('actividades', 'actividades');
        $rsm->addScalarResult('rubros', 'rubros');
        $rsm->addScalarResult('denominacionEstado', 'denominacionEstado');
        $rsm->addScalarResult('aliasTipoImportancia', 'aliasTipoImportancia');
        $rsm->addScalarResult('codigoproveedor', 'codigoproveedor');
        $rsm->addScalarResult('representantelegal', 'representantelegal');
        $rsm->addScalarResult('extrajero', 'extrajero');
        $rsm->addScalarResult('dc_direccion', 'dc_direccion');
        $rsm->addScalarResult('dl_direccion', 'dl_direccion');
        $rsm->addScalarResult('contactos', 'contactos');
        $rsm->addScalarResult('numeroIIBB', 'numeroIIBB');
        $rsm->addScalarResult('condicionIVA', 'condicionIVA');
        $rsm->addScalarResult('exentoIVA', 'exentoIVA');
        $rsm->addScalarResult('condicionGANANCIAS', 'condicionGANANCIAS');
        $rsm->addScalarResult('exentoGANANCIAS', 'exentoGANANCIAS');
        $rsm->addScalarResult('condicionSUSS', 'condicionSUSS');
        $rsm->addScalarResult('exentoSUSS', 'exentoSUSS');
        $rsm->addScalarResult('condicionIIBB', 'condicionIIBB');
        $rsm->addScalarResult('exentoIIBB', 'exentoIIBB');
        $rsm->addScalarResult('calificacionfiscal', 'calificacionfiscal');
        $rsm->addScalarResult('problemasafip', 'problemasafip');
        $rsm->addScalarResult('riesgofiscal', 'riesgofiscal');
        $rsm->addScalarResult('magnitudessuperadas', 'magnitudessuperadas');
        $rsm->addScalarResult('calificacion', 'calificacion');
        $rsm->addScalarResult('claseCalificacionFinal', 'claseCalificacionFinal');

        $native_query = $em->createNativeQuery('SELECT * FROM vistaproveedores', $rsm);

        $proveedores = $native_query->getResult();

        return $this->render('ADIFComprasBundle:Proveedor:index_table.html.twig', array('proveedores' => $proveedores));
    }

    /**
     * Tabla para Proveedor.
     *
     * @Route("/index_table_extendido/", name="proveedor_index_table_extendido")
     * @Method("GET|POST")
     */
    public function indexTableExtendidoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('cuitDni', 'cuitDni');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('actividades', 'actividades');
        $rsm->addScalarResult('rubros', 'rubros');
        $rsm->addScalarResult('denominacionEstado', 'denominacionEstado');
        $rsm->addScalarResult('aliasTipoImportancia', 'aliasTipoImportancia');
        $rsm->addScalarResult('codigoproveedor', 'codigoproveedor');
        $rsm->addScalarResult('representantelegal', 'representantelegal');
        $rsm->addScalarResult('extrajero', 'extrajero');
        $rsm->addScalarResult('dc_direccion', 'dc_direccion');
        $rsm->addScalarResult('dl_direccion', 'dl_direccion');
        $rsm->addScalarResult('contactos', 'contactos');
        $rsm->addScalarResult('numeroIIBB', 'numeroIIBB');
        $rsm->addScalarResult('condicionIVA', 'condicionIVA');
        $rsm->addScalarResult('exentoIVA', 'exentoIVA');
        $rsm->addScalarResult('condicionGANANCIAS', 'condicionGANANCIAS');
        $rsm->addScalarResult('exentoGANANCIAS', 'exentoGANANCIAS');
        $rsm->addScalarResult('condicionSUSS', 'condicionSUSS');
        $rsm->addScalarResult('exentoSUSS', 'exentoSUSS');
        $rsm->addScalarResult('condicionIIBB', 'condicionIIBB');
        $rsm->addScalarResult('exentoIIBB', 'exentoIIBB');
        $rsm->addScalarResult('calificacionfiscal', 'calificacionfiscal');
        $rsm->addScalarResult('problemasafip', 'problemasafip');
        $rsm->addScalarResult('riesgofiscal', 'riesgofiscal');
        $rsm->addScalarResult('magnitudessuperadas', 'magnitudessuperadas');
        $rsm->addScalarResult('calificacion', 'calificacion');
        $rsm->addScalarResult('claseCalificacionFinal', 'claseCalificacionFinal');
        $rsm->addScalarResult('pasibleRetencionIva', 'pasibleRetencionIva');
        $rsm->addScalarResult('pasibleRetencionSUSS', 'pasibleRetencionSUSS');
        $rsm->addScalarResult('pasibleRetencionIngresosBrutos', 'pasibleRetencionIngresosBrutos');
        $rsm->addScalarResult('pasibleRetencionGanancias', 'pasibleRetencionGanancias');

        $native_query = $em->createNativeQuery('
            SELECT
            *
            FROM
                vistaproveedores_extendido            
      
        ', $rsm);

        $proveedores = $native_query->getResult();





        return $this->render('ADIFComprasBundle:Proveedor:index_table_extendido.html.twig', array('proveedores' => $proveedores)
        );
    }

    /**
     * @Route("/autocomplete/form", name="autocomplete_proveedor")
     */
    public function getProveedoresAction(Request $request) {

        $term = $request->query->get('term', null);
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
                     
        $query = $em
                    ->getRepository('ADIFComprasBundle:Proveedor')
                    ->createQueryBuilder('p')
                    ->innerJoin('p.clienteProveedor', 'cp')
                    ->innerJoin('p.estadoProveedor', 'ep')
                    ->where('upper(cp.razonSocial) LIKE :term')
                    ->orWhere('cp.CUIT LIKE :term')
                    ->orderBy('cp.razonSocial', 'DESC')
                    ->setParameter('term', '%' . strtoupper($term) . '%');
        
        if ($idEmpresa == 1) {
            // Es adif
            $query->andWhere('ep.id = 1');
        }
        
        $proveedores = $query
                            ->getQuery()
                            ->getResult();
         
       // \Doctrine\Common\Util\Debug::dump( $proveedores ); exit;
        
        $jsonResult = [];

        foreach ($proveedores as $proveedor) {
            $jsonResult[] = array(
                'id' => $proveedor->getId(),
                'razonSocial' => $proveedor->getClienteProveedor()->getRazonSocial(),
                'CUIT' => $proveedor->getClienteProveedor()->getCUIT(),
                'condicionIVA' => $proveedor->getClienteProveedor()->getCondicionIVA() ? $proveedor->getClienteProveedor()->getCondicionIVA()->__toString() : '',
                'cais' => $proveedor->getCaisPorPuntoVenta()
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * 
     * @param type $em
     * @param Proveedor $proveedor
     */
    private function chequearHistoricoCondicionFiscal($em, Proveedor $proveedor) {

        $clienteProveedor = null;

        if ($proveedor->getClienteProveedor()->getId() != null) {

            $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                    ->find($proveedor->getClienteProveedor()->getId());
        }

        if (null != $clienteProveedor) {
            $this->cerrarHistoricoCondicionFiscalAnteriores($em, $proveedor);
        }

        $this->crearHistoricoCondicionFiscal($em, $proveedor);
    }

    /**
     * 
     * @param type $em
     * @param Proveedor $proveedor
     */
    private function crearHistoricoCondicionFiscal($em, Proveedor $proveedor) {

// ClienteProveedorHistoricoGanancias
        $historicoProveedorGanancias = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoGanancias();

        $historicoProveedorGanancias->setFechaDesde(new \DateTime());
        $historicoProveedorGanancias->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorGanancias->setCondicion($proveedor->getClienteProveedor()->getCondicionGanancias());
        $historicoProveedorGanancias->setExento($proveedor->getClienteProveedor()->getExentoGanancias());
        $historicoProveedorGanancias->setPasibleRetencion($proveedor->getPasibleRetencionGanancias());

        if (null != $proveedor->getCertificadoExencionGanancias()) {
            $historicoProveedorGanancias->setCertificadoExencion(clone $proveedor->getCertificadoExencionGanancias());
        }

        $em->persist($historicoProveedorGanancias);
        /* FIN ClienteProveedorHistoricoGanancias */


// HistoricoProveedorIIBB
        $historicoProveedorIIBB = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoIIBB();

        $historicoProveedorIIBB->setFechaDesde(new \DateTime());
        $historicoProveedorIIBB->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorIIBB->setCondicion($proveedor->getClienteProveedor()->getCondicionIngresosBrutos());
        $historicoProveedorIIBB->setExento($proveedor->getClienteProveedor()->getExentoIngresosBrutos());
        $historicoProveedorIIBB->setPasibleRetencion($proveedor->getPasibleRetencionIngresosBrutos());

        if (null != $proveedor->getCertificadoExencionIngresosBrutos()) {
            $historicoProveedorIIBB->setCertificadoExencion(clone $proveedor->getCertificadoExencionIngresosBrutos());
        }

        $convenioMultilateral = $proveedor->getClienteProveedor()
                ->getConvenioMultilateralIngresosBrutos();

        if (null != $convenioMultilateral) {

            $historicoProveedorIIBB->setJurisdiccion($convenioMultilateral->getJurisdiccion());
            $historicoProveedorIIBB->setPorcentajeAplicacionCABA($convenioMultilateral
                            ->getPorcentajeAplicacionCABA()
            );
        }

        $em->persist($historicoProveedorIIBB);
        /* FIN HistoricoProveedorIIBB */


// ClienteProveedorHistoricoIVA
        $historicoProveedorIVA = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoIVA();

        $historicoProveedorIVA->setFechaDesde(new \DateTime());
        $historicoProveedorIVA->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorIVA->setCondicion($proveedor->getClienteProveedor()->getCondicionIVA());
        $historicoProveedorIVA->setExento($proveedor->getClienteProveedor()->getExentoIVA());
        $historicoProveedorIVA->setPasibleRetencion($proveedor->getPasibleRetencionIVA());

        if (null != $proveedor->getCertificadoExencionIVA()) {
            $historicoProveedorIVA->setCertificadoExencion(clone $proveedor->getCertificadoExencionIVA());
        }

        $em->persist($historicoProveedorIVA);
        /* FIN ClienteProveedorHistoricoIVA */


// ClienteProveedorHistoricoSUSS
        $historicoProveedorSUSS = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoSUSS();

        $historicoProveedorSUSS->setFechaDesde(new \DateTime());
        $historicoProveedorSUSS->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorSUSS->setCondicion($proveedor->getClienteProveedor()->getCondicionSUSS());
        $historicoProveedorSUSS->setExento($proveedor->getClienteProveedor()->getExentoSUSS());
        $historicoProveedorSUSS->setPasibleRetencion($proveedor->getPasibleRetencionSUSS());

        if (null != $proveedor->getCertificadoExencionSUSS()) {
            $historicoProveedorSUSS->setCertificadoExencion(clone $proveedor->getCertificadoExencionSUSS());
        }

        $em->persist($historicoProveedorSUSS);
        /* FIN ClienteProveedorHistoricoSUSS */
    }

    /**
     * 
     * @param type $em
     * @param Proveedor $proveedor
     */
    private function cerrarHistoricoCondicionFiscalAnteriores($em, Proveedor $proveedor) {

        $now = new \DateTime();

        /* @var ClienteProveedorHistoricoIIBB */
        $historicoProveedorGanancias = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoGanancias')->
                findOneBy(
                array('clienteProveedor' => $proveedor->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoProveedorGanancias->setFechaHasta($now);

        $em->persist($historicoProveedorGanancias);


        /* @var ClienteProveedorHistoricoIIBB */
        $historicoProveedorIIBB = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoIIBB')->
                findOneBy(
                array('clienteProveedor' => $proveedor->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoProveedorIIBB->setFechaHasta($now);

        $em->persist($historicoProveedorIIBB);


        /* @var ClienteProveedorHistoricoIVA */
        $historicoProveedorIVA = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoIVA')->
                findOneBy(
                array('clienteProveedor' => $proveedor->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoProveedorIVA->setFechaHasta($now);

        $em->persist($historicoProveedorIVA);


        /* @var ClienteProveedorHistoricoSUSS */
        $historicoProveedorSUSS = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoSUSS')->
                findOneBy(
                array('clienteProveedor' => $proveedor->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoProveedorSUSS->setFechaHasta($now);

        $em->persist($historicoProveedorSUSS);
    }

    /**
     * Muestra la cuenta corriente del proveedor.
     *
     * @Route("/{idProveedor}/cuentacorriente", name="proveedor_cta_cte")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:cuenta_corriente.html.twig")
     */
    public function cuentaCorrienteIndexAction($idProveedor) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmContable());

        /* @var Proveedor */
        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = $this->generateUrl('proveedor');
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', ['id' => $proveedor->getId()]);
        $bread['Cuenta corriente'] = null;

        $repository_cc = $em_contable->getRepository('ADIFContableBundle:ComprobanteCompra');

        $tiposComprobantesDeuda = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA
        ];

        $ordenesCompra = [];

        foreach ($proveedor->getOrdenesCompra() as $oc) {

            $oc_array = [
                'saldo' => 0,
                'id' => $oc->getId(),
                'numero' => $oc->getNumeroOrdenCompra(),
                'monto' => $oc->getMonto()
            ];

            $comprobantes = $repository_cc->getComprobantesCompraByOrdenCompra($oc->getId());

// CANCELAN
            $ordenesPago['ids'] = array();

            foreach ($comprobantes as $comprobante) {

                if ($comprobante->getOrdenPago() != null) {
                    $op = $comprobante->getOrdenPago();
                    if (!in_array($op->getId(), $ordenesPago['ids'])) {
                        $ordenesPago['ids'][] = $op->getId();
                        $oc_array['saldo'] += $op->getTotalBruto();
                    }
                }

                $oc_array['saldo'] += $comprobante->getTotal() *
                        (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesDeuda) ? -1 : 1);
            }

            $ordenesCompra[] = $oc_array;
        }

        return array(
            'ocs' => $ordenesCompra,
            'proveedor' => $proveedor,
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Muestra los comprobantes de una OC para la cta cte
     *
     * @Route("/{idProveedor}/cuentacorriente/{idOrdenCompra}", name="proveedor_cta_cte_detalle")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:cuenta_corriente_detalle.html.twig")
     */
    public function cuentaCorrienteDetalleAction($idProveedor, $idOrdenCompra) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmContable());
        $em_compras = $this->getDoctrine()->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmCompras());

        /* @var Proveedor */
        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = $this->generateUrl('proveedor');
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', ['id' => $proveedor->getId()]);
        $bread['Cuenta corriente'] = $this->generateUrl('proveedor_cta_cte', ['idProveedor' => $idProveedor]);
        $bread['Comprobantes'] = null;


        $tiposComprobantesDeuda = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA
        ];

        $tiposComprobantesCredito = [
            ConstanteTipoComprobanteCompra::NOTA_CREDITO
        ];

        $repository_cc = $em_contable->getRepository('ADIFContableBundle:ComprobanteCompra');
        $repository_oc = $em_compras->getRepository('ADIFComprasBundle:OrdenCompra');

        $comprobantes = $repository_cc->getComprobantesCompraByOrdenCompra($idOrdenCompra);
        $ordenCompra = $repository_oc->find($idOrdenCompra);

        $comprobantes_result = [];

// CANCELAN
        $ordenesPago = [
            'ids' => []
        ];

        foreach ($comprobantes as $comprobante) {
            $comprobantes_result[] = [
                'id' => $comprobante->getId(),
                'numero' => $comprobante->getNumeroCompleto(),
                'fecha' => $comprobante->getFechaComprobante(),
                'tipo' => $comprobante->getTipoComprobante(),
                'monto' => $comprobante->getTotal(),
                'deuda' => in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesDeuda),
                'es_op' => false
            ];

            if ($comprobante->getOrdenPago() != null) {
                $op = $comprobante->getOrdenPago();
                if (!in_array($op->getId(), $ordenesPago['ids'])) {
                    $ordenesPago['ids'][] = $op->getId();

// Se agrega a los comprobantes que restan deuda
                    $comprobantes_result[] = [
                        'id' => $op->getId(),
                        'numero' => $op->getNumeroOrdenPago(),
                        'fecha' => $op->getFechaOrdenPago(),
                        'tipo' => 'Orden de pago',
                        'monto' => $op->getTotalBruto(),
                        'deuda' => false,
                        'es_op' => true
                    ];
                }
            }
        }

        return array(
            'orden_compra' => $ordenCompra,
            // 'comprobantes' => $comprobantes,
            'comprobantes_html' => $comprobantes_result,
            'proveedor' => $proveedor,
            'tc_deuda' => $tiposComprobantesDeuda,
            'tc_credito' => $tiposComprobantesCredito,
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor | Cuenta corriente',
            'page_info' => 'Cuenta corriente - Detalle'
        );
    }

    /**
     * Muestra los comprobantes de un tramo para la cta cte de obras
     *
     * @Route("/{idProveedor}/cuentacorrienteobras/{idTramo}", name="proveedor_cta_cte_obras_detalle")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:cuenta_corriente_obras_detalle.html.twig")
     */
    public function cuentaCorrienteObrasDetalleAction($idProveedor, $idTramo) {

        $em_contable = $this->getDoctrine()->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmContable());
        $em_compras = $this->getDoctrine()->getManager(\ADIF\BaseBundle\Entity\EntityManagers::getEmCompras());

        $repository_tramo = $em_contable->getRepository('ADIFContableBundle:Obras\Tramo');
        $repository_co = $em_contable->getRepository('ADIFContableBundle:Obras\ComprobanteObra');

        /* @var Proveedor */
        $proveedor = $em_compras->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = $this->generateUrl('proveedor');
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', ['id' => $proveedor->getId()]);
        $bread['Cuenta corriente obras'] = $this->generateUrl('proveedor_cta_cte_obras', ['idProveedor' => $idProveedor]);
        $bread['Comprobantes'] = null;

        $tiposComprobantesDeuda = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA
        ];

        $tiposComprobantesCredito = [
            ConstanteTipoComprobanteCompra::NOTA_CREDITO
        ];

        $comprobantes = $repository_co->getComprobantesObraByTramo($idTramo);
        $tramo = $repository_tramo->find($idTramo);

        $comprobantes_result = [];

// CANCELAN
        $ordenesPago = [
            'ids' => []
        ];

        foreach ($comprobantes as $comprobante) {
            $comprobantes_result[] = [
                'id' => $comprobante->getId(),
                'numero' => $comprobante->getNumeroCompleto(),
                'fecha' => $comprobante->getFechaComprobante(),
                'tipo' => $comprobante->getTipoComprobante(),
                'monto' => $comprobante->getTotal(),
                'deuda' => in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesDeuda),
                'es_op' => false
            ];

            if ($comprobante->getOrdenPago() != null) {
                $op = $comprobante->getOrdenPago();
                if (!in_array($op->getId(), $ordenesPago['ids'])) {
                    $ordenesPago['ids'] = $op->getId();

// Se agrega a los comprobantes que restan deuda
                    $comprobantes_result[] = [
                        'id' => $op->getId(),
                        'numero' => $op->getNumeroOrdenPago(),
                        'fecha' => $op->getFechaOrdenPago(),
                        'tipo' => 'Orden de pago',
                        'monto' => $op->getTotalBruto(),
                        'deuda' => false,
                        'es_op' => true
                    ];
                }
            }
        }

        return array(
            'tramo' => $tramo,
            'comprobantes_html' => $comprobantes_result,
            'proveedor' => $proveedor,
            'tc_deuda' => $tiposComprobantesDeuda,
            'tc_credito' => $tiposComprobantesCredito,
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor | Detalle de cuenta corriente obra',
            'page_info' => 'Cuenta corriente de obras - Detalle'
        );
    }

    /**
     * Muestra la cuenta corriente obras del proveedor.
     *
     * @Route("/{idProveedor}/cuentacorrienteobras", name="proveedor_cta_cte_obras")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:cuenta_corriente_obras.html.twig")
     */
    public function cuentaCorrienteObrasIndexAction($idProveedor) {
        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $repository_tramo = $em_contable->getRepository('ADIFContableBundle:Obras\Tramo');
        $tramos = $repository_tramo->findByIdProveedor($idProveedor);

        $proveedor = $em_compras->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

// Debug::dump($proveedor);

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = $this->generateUrl('proveedor');
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', ['id' => $proveedor->getId()]);
        $bread['Cuenta corriente de obras'] = null;

        $tiposComprobantesDeuda = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA
        ];

        $tiposComprobantesCredito = [
            ConstanteTipoComprobanteCompra::NOTA_CREDITO
        ];

        $tramos_array = [];

        $repository_o = $em_contable->getRepository('ADIFContableBundle:Obras\ComprobanteObra');
// $repository_roc = $em->getRepository('ADIFComprasBundle:RenglonOrdenCompra');

        foreach ($tramos as $tramo) {
            $saldo_tramo = 0;
            $saldo_ejecutado = 0;
            $comprobantes = $repository_o->getComprobantesObraByTramo($tramo->getId());

// CANCELAN
            $ordenesPago = [
                'ids' => []
            ];

            foreach ($comprobantes as $comprobante) {

                if ($comprobante->getOrdenPago() != null) {
                    $op = $comprobante->getOrdenPago();
                    if (!in_array($op->getId(), $ordenesPago['ids'])) {
                        $ordenesPago['ids'] = $op->getId();
                        $saldo_ejecutado += $op->getTotalBruto();
                    }
                } else {
                    $saldo_tramo += $comprobante->getTotal() *
                            (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesDeuda) ? -1 : 1);
                }
            }

            $tramos_array[] = [
                'tramo' => $tramo,
                'saldo' => $saldo_tramo,
                'saldo_ejecutado' => $saldo_ejecutado
            ];
        }

        return array(
            'proveedor' => $proveedor,
            'tramos' => $tramos_array,
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor | Cuenta corriente obra',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * @Route("/importarRiesgoFiscal", name="importar_riesgoFiscal")
     * 
     */
    public function importarRiesgoFiscal(Request $request) {

        $dir = __DIR__ . '/../../../../web/uploads/riesgoFiscal';

        $filename = time();

        $ext = '.zip';

        try {
            $request->files->all()['form_importar_riesgoFiscal_file']->move($dir, $filename . $ext);

// Descomprimo el archivo
            $zip = new ZipArchive;

            $res = $zip->open($dir . '/' . $filename . $ext);

            if ($res === TRUE) {

                $filezip = $zip->getNameIndex(0);

                $zip->extractTo($dir, $filezip);

                rename($dir . '/' . $filezip, $dir . '/' . $filename . '.txt');

                $zip->close();
            } else {
                $this->get('session')->getFlashBag()
                        ->add('error', "No se encuentra el archivo (.ZIP)");
            }

// Listo todos los proveedores
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $entities = $em->getRepository('ADIFComprasBundle:Proveedor')->findAll();

            if (!$entities) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
            }

            $cuits = array();

            foreach ($entities as $proveedor) {
                $cuits[] = $proveedor->getClienteProveedor()->getCuit();
            }

// Abro el archivo y busco cada cuit en el listado de cuits de proveedores de adif
            $cant = 0;

            if (($file = fopen($dir . '/' . $filename . '.txt', 'r')) !== FALSE) {

                while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {

                    $cuit = substr($row[self::RF_COLUMN_INDEX_CUIT], 0, 2) . '-' . substr($row[self::RF_COLUMN_INDEX_CUIT], 2, 8) . '-' . substr($row[self::RF_COLUMN_INDEX_CUIT], 10, 2);

// Si tiene riesgo fiscal
                    if (in_array($cuit, $cuits)) {

                        $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                                ->findOneBy(array('CUIT' => $cuit));

                        $clienteProveedor->setTieneRiesgoFiscal(TRUE);

                        $cant = $cant + 1;
                    }
                }
                fclose($file);

                $em->flush();
            } else {
                $this->get('session')->getFlashBag()
                        ->add('error', "Error al abrir el archivo(.TXT)");
            }

// Borro el zip y txt
            unlink($dir . '/' . $filename . $ext);
            unlink($dir . '/' . $filename . '.txt');

            if (($res === TRUE) & $file !== FALSE) {
                $this->get('session')->getFlashBag()
                        ->add('success', "La imporaciÃ³n se realizÃ³ con Ã©xito. Se actualizaron $cant registros");
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()
                    ->add('error', "Se produjeron errores en la importaciÃ³n.");
        }
        return $this->redirect($this->generateUrl('proveedor'));
    }

    /**
     * @Route("/importarMagnitudes", name="importar_magnitudes")
     * 
     */
    public function importarMagnitudes(Request $request) {

        $dir = __DIR__ . '/../../../../web/uploads/magnitudes';

        $filename = time();

        $ext = '.rar';

        try {
            $request->files->all()['form_importar_magnitudes_file']->move($dir, $filename . $ext);

// Descomprimo el archivo RAR
            $rar = \RarArchive::open($dir . '/' . $filename . $ext);

            if ($rar === FALSE) {
                $this->get('session')->getFlashBag()
                        ->add('error', "No se encuentra el archivo (.RAR)");
            } else {
                $entries = $rar->getEntries();

                $filerar = $entries[0]->getName();

                $entries[0]->extract($dir . '/');

                rename($dir . '/' . $filerar, $dir . '/' . $filename . '.txt');

                $rar->close();
            }

// Listo todos los proveedores
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $entities = $em->getRepository('ADIFComprasBundle:Proveedor')->findAll();

            if (!$entities) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
            }

// Me traigo todo los proveedores
            $cuits = array();

            foreach ($entities as $proveedor) {
                $cuits[] = $proveedor->getClienteProveedor()->getCuit();
            }

// Abro el archivo y busco cada cuit en el listado de cuits de proveedores de adif
            $cant = 0;

            if (($file = fopen($dir . '/' . $filename . '.txt', 'r')) !== FALSE) {

                while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {

                    $cuit = substr($row[self::MS_COLUMN_INDEX_CUIT], 0, 2) . '-' . substr($row[self::MS_COLUMN_INDEX_CUIT], 2, 8) . '-' . substr($row[self::MS_COLUMN_INDEX_CUIT], 10, 2);

// Si incluye magnitudes superadas
                    if ((in_array($cuit, $cuits)) && ($row[self::MS_COLUMN_INDEX_ALICUOTA_PERCEPCION] > 0) && ($row[self::MS_COLUMN_INDEX_ALICUOTA_RETENCION] > 0)) {

                        $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                                ->findOneBy(array('CUIT' => $cuit));

                        $clienteProveedor->setIncluyeMagnitudesSuperadas(TRUE);

                        $cant = $cant + 1;
                    }
                }

                fclose($file);

                $em->flush();
            } else {
                $this->get('session')->getFlashBag()
                        ->add('error', "Error al abrir el archivo(.TXT)");
            }

// Borro el rar y txt
            unlink($dir . '/' . $filename . $ext);
            unlink($dir . '/' . $filename . '.txt');

            if (($rar !== FALSE) & $file !== FALSE) {
                $this->get('session')->getFlashBag()
                        ->add('success', "La imporaciÃ³n se realizÃ³ con Ã©xito. Se actualizaron $cant registros");
            }
        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()
                    ->add('error', "Se produjeron errores en la importaciÃ³n.");
        }

        return $this->redirect($this->generateUrl('proveedor'));
    }
    
	/**
     * Muestra la cuenta corriente del proveeedor.
     *
     * @Route("/cuentacorrientedetalletotal/{idProveedor}/", name="proveedor_cta_cte_detalle_total")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:cuenta_corriente_detalle_total.html.twig")
     */
    public function cuentaCorrienteDetalleTotalIndexAction($idProveedor) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $proveedor Proveedor */
        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = $this->generateUrl('proveedor');
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', ['id' => $proveedor->getId()]);
        $bread['Cuenta corriente'] = null;

        return array(
            'proveedor' => $proveedor,
            'breadcrumbs' => $bread,
            'page_title' => 'Proveedor | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }


    /**
     * @Route("/filtrar_cuentacorrientedetalletotal/", name="proveedor_filtrar_cta_cte_detalle_total")
     */
    public function filtrarCuentaCorrienteDetalleTotalIndexAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $idProveedor = $request->request->get('idProveedor');

        $fecha = \DateTime::createFromFormat('d/m/Y H:i:s', $request->request->get('fecha') . ' 23:59:59');

        /* @var $proveedor Proveedor */
        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Proveedores'] = $this->generateUrl('proveedor');
        $bread[$proveedor->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('proveedor_show', ['id' => $proveedor->getId()]);
        $bread['Cuenta corriente'] = null;

        $ordenesCompra = array();
        $tramos = array();
        $saldoTotal = $comprobante_saldo = 0;

        //COMPRAS
        $repository_cc = $em_contable->getRepository('ADIFContableBundle:ComprobanteCompra');

        foreach ($proveedor->getOrdenesCompraFinal() as $oc) {
            $ordenesCompra[$oc->getId()] = [
                'id' => $oc->getId(),
                'nombre' => $oc->__toString(),
                'es_servicio' => $oc->getEsServicio(),
                'total' => $oc->getMonto(),
                'saldo' => 0,
                'comprobantes' => array(),
                'anticipos' => array(),
                'ordenes_pago' => array()
            ];

            $comprobantes = $repository_cc->getComprobantesCompraByOrdenCompraYFecha($oc->getId(), $fecha);

            /* @var $ultimoComprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
            $ultimoComprobante = null;

            /* @var $comprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
            foreach ($comprobantes as $comprobante) {
                if ($ultimoComprobante == null) {
                    $ultimoComprobante = $comprobante;
                }

                if ($comprobante->getId() == $ultimoComprobante->getId() ||
                        (!($comprobante->isEqualTo($ultimoComprobante) //                        
                        && $ultimoComprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO //
                        && ($ultimoComprobante->getFechaAnulacion() != null && $ultimoComprobante->getFechaAnulacion() > $fecha)))) {

                    if ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::CUPON) {
                        $comprobante_anulado = $comprobante->getEstaAnuladoALaFecha($fecha);
                        $comprobante_saldo = $comprobante->getSaldoALaFecha($fecha);

                        $comprobante_array = [
                            'id' => $comprobante->getId(),
                            'fecha' => $comprobante->getFechaComprobante(),
                            'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                            'numero' => $comprobante->getNumeroCompleto(),
                            'monto' => $comprobante->getTotal(),
                            'saldo' => $comprobante_saldo,
                            'anulado' => $comprobante_anulado,
                            'restaSaldo' => false,
                            'pagos_parciales' => array(),
                            'notas_credito' => array(),
							'comprobantes_ajustes' => array()
                        ];
						
						// Me fijo si el comprobante tiene ajustes
						//if (!empty($comprobante->getComprobantesAjustes())) {
							//$comprobante_array[$comprobante->getId()]['comprobantes_ajustes'] = $comprobante->getComprobantesAjustes();
						//} 

                        if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                            $comprobante_array['restaSaldo'] = false;
                            if ($comprobante->getEsNotaCredito()) {
                                $comprobante_array['restaSaldo'] = true;
                            }
                        }

                        $ordenesCompra[$oc->getId()]['saldo'] += $comprobante_array['restaSaldo'] ? $comprobante_saldo * -1 : $comprobante_saldo;

                        /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                        foreach ($comprobante->getPagosParcialesPagosALaFecha($fecha) as $pagoParcial) {
                            $pago_parcial_array = [
                                'id' => $pagoParcial->getId(),
                                'fecha' => $pagoParcial->getFechaPago(),
                                'tipoComprobante' => 'Pago parcial',
                                'numero' => '-',
                                'monto' => $pagoParcial->getImporte(),
                                'saldo' => 0,
                                'anulado' => $comprobante_anulado,
                                'restaSaldo' => true
                            ];

                            $comprobante_array['pagos_parciales'][] = $pago_parcial_array;
                        }

                        foreach ($comprobante->getComprobantesAcreditadosNoAnuladosALaFecha($fecha) as $notaCreditoComprobante) {
                            $notaCredito = $notaCreditoComprobante->getNotaCredito();
                            $montoAcreditado = 0;

                            /* @var $renglonComprobante \ADIF\ContableBundle\Entity\RenglonComprobante */
                            foreach ($notaCredito->getRenglonesComprobante() as $renglonComprobante) {
                                if ($renglonComprobante->getRenglonAcreditado()->getComprobante()->getId() == $comprobante->getId()) {

                                    $montoAcreditado += $renglonComprobante->getMontoBruto();
                                }
                            }
                            if ($montoAcreditado > 0) {
                                $nota_credito_array = [
                                    'id' => $notaCredito->getId(),
                                    'fecha' => $notaCredito->getFechaComprobante(),
                                    'tipoComprobante' => $notaCredito->getTipoComprobante()->getNombre() . (($notaCredito->getLetraComprobante() != null) ? ' (' . $notaCredito->getLetraComprobante()->getLetra() . ')' : ''),
                                    'numero' => $notaCredito->getNumeroCompleto(),
                                    'monto' => $montoAcreditado,
                                    'saldo' => 0,
                                    'anulado' => false,
                                    'restaSaldo' => true
                                ];

                                $comprobante_array['notas_credito'][] = $nota_credito_array;
                            }
                        }
						
						// Comprobantes de ajustes
						$arrComprobantesAjustes = array();
						foreach($comprobante->getComprobantesAjustes() as $comprobanteAjuste) {
							$arrComprobantesAjustes = [
                                    'id' => $comprobanteAjuste->getId(),
                                    'fecha' => $comprobanteAjuste->getFechaComprobante(),
                                    'tipoComprobante' => $comprobanteAjuste->getDetalle(),
                                    'numero' => '-',
                                    'monto' => $comprobanteAjuste->getTotal(),
                                    'saldo' => $comprobante_saldo,
                                    'anulado' => ($comprobanteAjuste->getFechaAnulacion() != null),
                                    'restaSaldo' => ($comprobanteAjuste->getEsNotaCredito()) ? true : false
                                ];
								
							$comprobante_array['comprobantes_ajustes'][] = $arrComprobantesAjustes;
							
						/*	
							$ordenesCompra[$oc->getId()]['saldo'] += ($comprobanteAjuste->getEsNotaCredito()) 
								? $comprobanteAjuste->getTotal() * -1 
								: $comprobanteAjuste->getTotal();
						*/
						
						}

                        if ($comprobante->getOrdenPago() != null) {
                            $op = $comprobante->getOrdenPago();
                            if ($op->getNumeroOrdenPago() != null && $op->getFechaContable() <= $fecha) {
                                if (!isset($ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()])) {
                                    $ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()] = [
                                        'id' => $op->getId(),
                                        'fecha' => $op->getFechaOrdenPago(),
                                        'tipoComprobante' => 'Orden de pago',
                                        'numero' => $op->getNumeroOrdenPago(),
                                        'monto' => $op->getTotalBruto(),
                                        'saldo' => 0,
                                        'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                        'restaSaldo' => true,
                                        'comprobantes' => array(),
                                        'anticipos' => array()
                                    ];
                                }

                                $ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()]['comprobantes'][] = $comprobante_array;

                                if ($op->getAnticipos() != null) {
                                    $anticipos = $op->getAnticipos();
                                    foreach ($anticipos as $anticipo) {
                                        $anticipo_array = [
                                            'id' => $anticipo->getId(),
                                            'fecha' => $anticipo->getFecha(),
                                            'tipoComprobante' => 'Anticipo',
                                            'numero' => '-',
                                            'monto' => $anticipo->getMonto(),
                                            'saldo' => 0,
                                            'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                            'restaSaldo' => true
                                        ];
                                        $ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()]['anticipos'][] = $anticipo_array;
                                    }
                                }
                            } else {
                                $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                            }
                        } else {
                            $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                        }
                    }
                }
                $ultimoComprobante = $comprobante;
            }

            //anticipos no cancelados con oc
            $anticiposNoAplicados = $em_contable->getRepository('ADIFContableBundle:AnticipoOrdenCompra')
                    ->createQueryBuilder('a')
                    ->innerJoin('a.ordenPago', 'opAnticipo')
                    ->leftJoin('ADIFContableBundle:OrdenPago', 'opCancelacion', Join::WITH, 'a.ordenPagoCancelada = opCancelacion.id AND (NOT (opCancelacion.fechaContable <= :fecha AND (opCancelacion.fechaAnulacion IS NULL OR opCancelacion.fechaAnulacion > :fecha)))')
                    ->where('a.idOrdenCompra = :idOC')
                    ->andWhere('opAnticipo.fechaContable <= :fecha AND (opAnticipo.fechaAnulacion IS NULL OR opAnticipo.fechaAnulacion > :fecha)')
                    ->setParameter('idOC', $oc->getId())
                    ->setParameter('fecha', $fecha->format('Y-m-d 23:59:59'))
                    ->getQuery()
                    ->getResult();

            foreach ($anticiposNoAplicados as $anticipo) {
                $saldo_anticipo = $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha) ? 0 : $anticipo->getMonto();
                $anticipo_array = [
                    'id' => $anticipo->getId(),
                    'fecha' => $anticipo->getFecha(),
                    'tipoComprobante' => 'Anticipo',
                    'numero' => '-',
                    'monto' => $anticipo->getMonto(),
                    'saldo' => $saldo_anticipo,
                    'anulado' => $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha),
                    'restaSaldo' => true
                ];
                $ordenesCompra[$oc->getId()]['anticipos'][] = $anticipo_array;
                $ordenesCompra[$oc->getId()]['saldo'] += $saldo_anticipo;
            }

            $saldoTotal += $ordenesCompra[$oc->getId()]['saldo'];
        }

        //OBRAS 
        $repository_co = $em_contable->getRepository('ADIFContableBundle:Obras\ComprobanteObra');

        $tramosProveedor = $em_contable->getRepository('ADIFContableBundle:Obras\Tramo')->findByIdProveedor($idProveedor);

        foreach ($tramosProveedor as $tramo) {
            /* @var $tramo \ADIF\ContableBundle\Entity\Obras\Tramo */
            $tramos[$tramo->getId()] = [
                'id' => $tramo->getId(),
                'nombre' => $tramo->__toString(),
                'total' => $tramo->getTotalContrato(true),
                'saldo' => 0,
                'comprobantes' => array(),
                'ordenes_pago' => array()
            ];

            $comprobantes = $repository_co->getComprobantesObraByTramoYFecha($tramo->getId(), $fecha);

            /* @var $ultimoComprobante \ADIF\ContableBundle\Entity\Obras\ComprobanteObra */
            $ultimoComprobante = null;

            /* @var $comprobante \ADIF\ContableBundle\Entity\Obras\ComprobanteObra */
            foreach ($comprobantes as $comprobante) {

                if ($ultimoComprobante == null) {
                    $ultimoComprobante = $comprobante;
                }

                if ($comprobante->getId() == $ultimoComprobante->getId() ||
                        (!($comprobante->isEqualTo($ultimoComprobante) //
                        && $ultimoComprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO //
                        && ($ultimoComprobante->getFechaAnulacion() != null && $ultimoComprobante->getFechaAnulacion() > $fecha)))) {


                    $comprobante_anulado = $comprobante->getEstaAnuladoALaFecha($fecha);
                    $comprobante_saldo = $comprobante->getSaldoALaFecha($fecha);

                    $comprobante_array = [
                        'id' => $comprobante->getId(),
                        'fecha' => $comprobante->getFechaComprobante(),
                        'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                        'numero' => $comprobante->getNumeroCompleto(),
                        'monto' => $comprobante->getTotal(),
                        'saldo' => $comprobante_saldo,
                        'anulado' => $comprobante_anulado,
                        'pagos_parciales' => array(),
                        'notas_credito' => array(),
						'comprobantes_ajustes' => array()
                    ];

                    if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteObra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                        $comprobante_array['restaSaldo'] = false;
                        if ($comprobante->getEsNotaCredito()) {
                            $comprobante_array['restaSaldo'] = true;
                        }
                    }

                    $tramos[$tramo->getId()]['saldo'] += $comprobante_array['restaSaldo'] ? $comprobante_saldo * -1 : $comprobante_saldo;

                    /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                    foreach ($comprobante->getPagosParcialesPagosALaFecha($fecha) as $pagoParcial) {
                        $pago_parcial_array = [
                            'id' => $pagoParcial->getId(),
                            'fecha' => $pagoParcial->getFechaPago(),
                            'tipoComprobante' => 'Pago parcial',
                            'numero' => '-',
                            'monto' => $pagoParcial->getImporte(),
                            'saldo' => 0,
                            'anulado' => $comprobante_anulado,
                            'restaSaldo' => true
                        ];

                        $comprobante_array['pagos_parciales'][] = $pago_parcial_array;
                    }

                    /* @var $notaCredito \ADIF\ContableBundle\Entity\NotaCreditoComprobante */
                    foreach ($comprobante->getComprobantesAcreditadosNoAnuladosALaFecha($fecha) as $notaCreditoComprobante) {
                        $notaCredito = $notaCreditoComprobante->getNotaCredito();
                        $montoAcreditado = 0;

                        /* @var $renglonComprobante \ADIF\ContableBundle\Entity\RenglonComprobante */
                        foreach ($notaCredito->getRenglonesComprobante() as $renglonComprobante) {
                            if ($renglonComprobante->getRenglonAcreditado()->getComprobante()->getId() == $comprobante->getId()) {

                                $montoAcreditado += $renglonComprobante->getMontoBruto();
                            }
                        }

                        if ($montoAcreditado > 0) {
                            $nota_credito_array = [
                                'id' => $notaCredito->getId(),
                                'fecha' => $notaCredito->getFechaComprobante(),
                                'tipoComprobante' => $notaCredito->getTipoComprobante()->getNombre() . (($notaCredito->getLetraComprobante() != null) ? ' (' . $notaCredito->getLetraComprobante()->getLetra() . ')' : ''),
                                'numero' => $notaCredito->getNumeroCompleto(),
                                'monto' => $montoAcreditado,
                                'saldo' => 0,
                                'anulado' => false,
                                'restaSaldo' => true
                            ];

                            $comprobante_array['notas_credito'][] = $nota_credito_array;
                        }
                    }
					
					// Comprobantes de ajustes
					$arrComprobantesAjustes = array();
					foreach($comprobante->getComprobantesAjustes() as $comprobanteAjuste) {
						$arrComprobantesAjustes = [
								'id' => $comprobanteAjuste->getId(),
								'fecha' => $comprobanteAjuste->getFechaComprobante(),
								'tipoComprobante' => $comprobanteAjuste->getDetalle(),
								'numero' => '-',
								'monto' => $comprobanteAjuste->getTotal(),
								'saldo' => $comprobante_saldo,
								'anulado' => ($comprobanteAjuste->getFechaAnulacion() != null),
								'restaSaldo' => ($comprobanteAjuste->getEsNotaCredito()) ? true : false
							];
							
						$comprobante_array['comprobantes_ajustes'][] = $arrComprobantesAjustes;
					/*	
						$tramos[$tramo->getId()]['saldo'] += ($comprobanteAjuste->getEsNotaCredito()) 
							? $comprobanteAjuste->getTotal() * -1 
							: $comprobanteAjuste->getTotal(); 
					*/
					
					}

                    if ($comprobante->getOrdenPago() != null) {
                        $op = $comprobante->getOrdenPago();

                        if ($op->getNumeroOrdenPago() != null && $op->getFechaContable() <= $fecha) {

                            if (!isset($tramos[$tramo->getId()]['ordenes_pago'][$op->getId()])) {
                                $tramos[$tramo->getId()]['ordenes_pago'][$op->getId()] = [
                                    'id' => $op->getId(),
                                    'fecha' => $op->getFechaOrdenPago(),
                                    'tipoComprobante' => 'Orden de pago',
                                    'numero' => $op->getNumeroOrdenPago(),
                                    'monto' => $op->getTotalBruto(),
                                    'saldo' => 0,
                                    'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                    'restaSaldo' => true,
                                    'comprobantes' => array(),
                                    'anticipos' => array()
                                ];
                            }

                            $tramos[$tramo->getId()]['ordenes_pago'][$op->getId()]['comprobantes'][] = $comprobante_array;

                            if ($op->getAnticipos() != null) {
                                $anticipos = $op->getAnticipos();
                                foreach ($anticipos as $anticipo) {
                                    $anticipo_array = [
                                        'id' => $anticipo->getId(),
                                        'fecha' => $anticipo->getFecha(),
                                        'tipoComprobante' => 'Anticipo',
                                        'numero' => '-',
                                        'monto' => $anticipo->getMonto(),
                                        'saldo' => 0,
                                        'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                        'restaSaldo' => true
                                    ];

                                    $tramos[$tramo->getId()]['ordenes_pago'][$op->getId()]['anticipos'][] = $anticipo_array;
                                }
                            }
                        } else {
                            $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                        }
                    } else {
                        $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                    }
                }

                $ultimoComprobante = $comprobante;
            }

            $saldoTotal += $tramos[$tramo->getId()]['saldo'];
        }

        //anticipos no cancelados sin oc
        $anticiposNoAplicadosSinOC = $em_contable->getRepository('ADIFContableBundle:AnticipoProveedor')
                ->createQueryBuilder('ap')
                ->innerJoin('ap.ordenPago', 'opAnticipo')
                ->leftJoin('ADIFContableBundle:OrdenPago', 'opCancelacion', Join::WITH, 'ap.ordenPagoCancelada = opCancelacion.id AND (NOT (opCancelacion.fechaContable <= :fecha AND (opCancelacion.fechaAnulacion IS NULL OR opCancelacion.fechaAnulacion > :fecha)))')
                ->where('ap.idProveedor = :idProv')
                ->andWhere('opAnticipo.fechaContable <= :fecha AND (opAnticipo.fechaAnulacion IS NULL OR opAnticipo.fechaAnulacion > :fecha)')
                ->andWhere('ap.ordenPagoCancelada IS NULL')
                ->setParameter('idProv', $proveedor->getId())
                ->setParameter('fecha', $fecha->format('Y-m-d 23:59:59'))
                ->getQuery()
                ->getResult();

        $anticiposSinOC = array();
        $saldoAnticiposSinOC = 0;

        foreach ($anticiposNoAplicadosSinOC as $anticipo) {
            $saldo_anticipo = $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha) ? 0 : $anticipo->getMonto();
            $anticiposSinOC[$anticipo->getId()] = [
                'id' => $anticipo->getId(),
                'fecha' => $anticipo->getFecha(),
                'tipoComprobante' => 'Anticipo',
                'numero' => '-',
                'monto' => $anticipo->getMonto(),
                'saldo' => $saldo_anticipo,
                'anulado' => $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha),
                'restaSaldo' => true
            ];
            $saldoAnticiposSinOC -= $saldo_anticipo;
        }

        $saldoTotal += $saldoAnticiposSinOC;
        
        return $this->render('ADIFComprasBundle:Proveedor:filtro_cuenta_corriente_detalle_total.html.twig', array(
                    'ordenesCompra' => $ordenesCompra,
                    'tramos' => $tramos,
                    'anticiposSinOC' => $anticiposSinOC,
                    'saldoAnticiposSinOC' => $saldoAnticiposSinOC,
                    'saldoTotal' => $saldoTotal,
                    'proveedor' => $proveedor
                        )
        );
    }

    /**
     * @Route("/filtrar_cuentacorrientedetalletotalsaldo/", name="proveedor_filtrar_cta_cte_detalle_total_saldo")
     */
    public function filtrarCuentaCorrienteDetalleTotalSaldoIndexAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $fecha = \DateTime::createFromFormat('d/m/Y H:i:s', $request->request->get('fecha') . ' 23:59:59');

        $saldos_proveedores = array();
        //$proveedores = $em->getRepository('ADIFComprasBundle:Proveedor')->findAll();

        $proveedores = $em->getRepository('ADIFComprasBundle:Proveedor')->createQueryBuilder('p')
                ->getQuery()
                ->setMaxResults(70)
                ->setFirstResult(560)
                ->getResult();

        foreach ($proveedores as $proveedor) {
            $idProveedor = $proveedor->getId();

            $ordenesCompra = array();
            $tramos = array();
            $saldoTotal = 0;

            //COMPRAS
            $repository_cc = $em_contable->getRepository('ADIFContableBundle:ComprobanteCompra');

            foreach ($proveedor->getOrdenesCompraFinal() as $oc) {

                $ordenesCompra[$oc->getId()] = [
                    'id' => $oc->getId(),
                    'nombre' => $oc->__toString(),
                    'es_servicio' => $oc->getEsServicio(),
                    'total' => $oc->getMonto(),
                    'saldo' => 0,
                    'comprobantes' => array(),
                    'anticipos' => array(),
                    'ordenes_pago' => array()
                ];

                $comprobantes = $repository_cc->getComprobantesCompraByOrdenCompraYFecha($oc->getId(), $fecha);

                /* @var $ultimoComprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
                $ultimoComprobante = null;

                /* @var $comprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
                foreach ($comprobantes as $comprobante) {

                    if ($ultimoComprobante == null) {
                        $ultimoComprobante = $comprobante;
                    }

//                echo 'ID comprobante = '.$comprobante->getId().'<br>';
//                echo 'ID ultimocomprobante = '.$ultimoComprobante->getId().'<br>';
//                echo 'Es igual = '.($comprobante->isEqualTo($ultimoComprobante) ? 'SI' : 'NO').'<br>';
//                echo 'Estado comprobante = '.$comprobante->getEstadoComprobante()->getNombre().'<br>';
//                echo 'Estado ultimocomprobante = '.$ultimoComprobante->getEstadoComprobante()->getNombre().'<br>';
//                echo 'Fecha anulacion ultimo comprobante = '. ($ultimoComprobante->getFechaAnulacion()? $ultimoComprobante->getFechaAnulacion()->format('d/m/Y')  : '') .'<br>';
//                echo '<hr><hr><hr>';

                    if ($comprobante->getId() == $ultimoComprobante->getId() ||
                            (!($comprobante->isEqualTo($ultimoComprobante) //
                            && $ultimoComprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO //
//                            && $comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO //
                            && ($ultimoComprobante->getFechaAnulacion() != null && $ultimoComprobante->getFechaAnulacion() > $fecha)))) {

                        if ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::CUPON) {
                            $comprobante_anulado = $comprobante->getEstaAnuladoALaFecha($fecha);
                            $comprobante_saldo = $comprobante->getSaldoALaFecha($fecha);

                            $comprobante_array = [
                                'id' => $comprobante->getId(),
                                'fecha' => $comprobante->getFechaComprobante(),
                                'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                                'numero' => $comprobante->getNumeroCompleto(),
                                'monto' => $comprobante->getTotal(),
                                'saldo' => $comprobante_saldo,
                                'anulado' => $comprobante_anulado,
                                'restaSaldo' => false,
                                'pagos_parciales' => array(),
                                'notas_credito' => array()
                            ];

                            if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                                $comprobante_array['restaSaldo'] = false;
                                if ($comprobante->getEsNotaCredito()) {
                                    $comprobante_array['restaSaldo'] = true;
                                }
                            }

                            $ordenesCompra[$oc->getId()]['saldo'] += $comprobante_array['restaSaldo'] ? $comprobante_saldo * -1 : $comprobante_saldo;

                            /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                            foreach ($comprobante->getPagosParcialesPagosALaFecha($fecha) as $pagoParcial) {
                                $pago_parcial_array = [
                                    'id' => $pagoParcial->getId(),
                                    'fecha' => $pagoParcial->getFechaPago(),
                                    'tipoComprobante' => 'Pago parcial',
                                    'numero' => '-',
                                    'monto' => $pagoParcial->getImporte(),
                                    'saldo' => 0,
                                    'anulado' => $comprobante_anulado,
                                    'restaSaldo' => true
                                ];

                                $comprobante_array['pagos_parciales'][] = $pago_parcial_array;
                            }

                            foreach ($comprobante->getComprobantesAcreditadosNoAnuladosALaFecha($fecha) as $notaCreditoComprobante) {
                                $notaCredito = $notaCreditoComprobante->getNotaCredito();
                                $montoAcreditado = 0;

                                /* @var $renglonComprobante \ADIF\ContableBundle\Entity\RenglonComprobante */
                                foreach ($notaCredito->getRenglonesComprobante() as $renglonComprobante) {
                                    if ($renglonComprobante->getRenglonAcreditado()->getComprobante()->getId() == $comprobante->getId()) {

                                        $montoAcreditado += $renglonComprobante->getMontoBruto();
                                    }
                                }
                                if ($montoAcreditado > 0) {
                                    $nota_credito_array = [
                                        'id' => $notaCredito->getId(),
                                        'fecha' => $notaCredito->getFechaComprobante(),
                                        'tipoComprobante' => $notaCredito->getTipoComprobante()->getNombre() . (($notaCredito->getLetraComprobante() != null) ? ' (' . $notaCredito->getLetraComprobante()->getLetra() . ')' : ''),
                                        'numero' => $notaCredito->getNumeroCompleto(),
                                        'monto' => $montoAcreditado,
                                        'saldo' => 0,
                                        'anulado' => false,
                                        'restaSaldo' => true
                                    ];

                                    $comprobante_array['notas_credito'][] = $nota_credito_array;
                                }
                            }

                            if ($comprobante->getOrdenPago() != null) {
                                $op = $comprobante->getOrdenPago();
                                if ($op->getNumeroOrdenPago() != null && $op->getFechaContable() <= $fecha) {
                                    if (!isset($ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()])) {
                                        $ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()] = [
                                            'id' => $op->getId(),
                                            'fecha' => $op->getFechaOrdenPago(),
                                            'tipoComprobante' => 'Orden de pago',
                                            'numero' => $op->getNumeroOrdenPago(),
                                            'monto' => $op->getTotalBruto(),
                                            'saldo' => 0,
                                            'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                            'restaSaldo' => true,
                                            'comprobantes' => array(),
                                            'anticipos' => array()
                                        ];
                                    }

                                    $ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()]['comprobantes'][] = $comprobante_array;

                                    if ($op->getAnticipos() != null) {
                                        $anticipos = $op->getAnticipos();
                                        foreach ($anticipos as $anticipo) {
                                            $anticipo_array = [
                                                'id' => $anticipo->getId(),
                                                'fecha' => $anticipo->getFecha(),
                                                'tipoComprobante' => 'Anticipo',
                                                'numero' => '-',
                                                'monto' => $anticipo->getMonto(),
                                                'saldo' => 0,
                                                'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                                'restaSaldo' => true
                                            ];
                                            $ordenesCompra[$oc->getId()]['ordenes_pago'][$op->getId()]['anticipos'][] = $anticipo_array;
                                        }
                                    }
                                    /*
                                      if (!isset($ordenesCompra[$oc->getId()]['ordenes_pago'][$orden_pago_array['id']])) {
                                      $ordenesCompra[$oc->getId()]['ordenes_pago'][$orden_pago_array['id']] = $orden_pago_array;
                                      }
                                     */
                                } else {
                                    $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                                }
                            } else {
                                $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                            }
                        }
                    }
                    $ultimoComprobante = $comprobante;
                }

                //anticipos no cancelados con oc
                $anticiposNoAplicados = $em_contable->getRepository('ADIFContableBundle:AnticipoOrdenCompra')
                        ->createQueryBuilder('a')
                        ->innerJoin('a.ordenPago', 'opAnticipo')
                        ->leftJoin('ADIFContableBundle:OrdenPago', 'opCancelacion', Join::WITH, 'a.ordenPagoCancelada = opCancelacion.id AND (NOT (opCancelacion.fechaContable <= :fecha AND (opCancelacion.fechaAnulacion IS NULL OR opCancelacion.fechaAnulacion > :fecha)))')
                        ->where('a.idOrdenCompra = :idOC')
                        ->andWhere('opAnticipo.fechaContable <= :fecha AND (opAnticipo.fechaAnulacion IS NULL OR opAnticipo.fechaAnulacion > :fecha)')
                        ->setParameter('idOC', $oc->getId())
                        ->setParameter('fecha', $fecha->format('Y-m-d 23:59:59'))
                        ->getQuery()
                        ->getResult();

                foreach ($anticiposNoAplicados as $anticipo) {
                    $saldo_anticipo = $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha) ? 0 : $anticipo->getMonto();
                    $anticipo_array = [
                        'id' => $anticipo->getId(),
                        'fecha' => $anticipo->getFecha(),
                        'tipoComprobante' => 'Anticipo',
                        'numero' => '-',
                        'monto' => $anticipo->getMonto(),
                        'saldo' => $saldo_anticipo,
                        'anulado' => $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha),
                        'restaSaldo' => true
                    ];
                    $ordenesCompra[$oc->getId()]['anticipos'][] = $anticipo_array;
                    $ordenesCompra[$oc->getId()]['saldo'] += $saldo_anticipo;
                }

                $saldoTotal += $ordenesCompra[$oc->getId()]['saldo'];
            }

            //OBRAS 
            $repository_co = $em_contable->getRepository('ADIFContableBundle:Obras\ComprobanteObra');

            $tramosProveedor = $em_contable->getRepository('ADIFContableBundle:Obras\Tramo')->findByIdProveedor($idProveedor);

            foreach ($tramosProveedor as $tramo) {
                /* @var $tramo \ADIF\ContableBundle\Entity\Obras\Tramo */
                $tramos[$tramo->getId()] = [
                    'id' => $tramo->getId(),
                    'nombre' => $tramo->__toString(),
                    'total' => $tramo->getTotalContrato(true),
                    'saldo' => 0,
                    'comprobantes' => array(),
                    'ordenes_pago' => array()
                ];

                $comprobantes = $repository_co->getComprobantesObraByTramoYFecha($tramo->getId(), $fecha);

                /* @var $ultimoComprobante \ADIF\ContableBundle\Entity\Obras\ComprobanteObra */
                $ultimoComprobante = null;


                /* @var $comprobante \ADIF\ContableBundle\Entity\Obras\ComprobanteObra */
                foreach ($comprobantes as $comprobante) {

                    if ($ultimoComprobante == null) {
                        $ultimoComprobante = $comprobante;
                    }

//                echo 'ID comprobante = '.$comprobante->getId().'<br>';
//                echo 'ID ultimocomprobante = '.$ultimoComprobante->getId().'<br>';
//                echo 'Es igual = '.($comprobante->isEqualTo($ultimoComprobante) ? 'SI' : 'NO').'<br>';
//                echo 'Estado comprobante = '.$comprobante->getEstadoComprobante()->getNombre().'<br>';
//                echo 'Estado ultimocomprobante = '.$ultimoComprobante->getEstadoComprobante()->getNombre().'<br>';
//                echo 'Fecha anulacion ultimo comprobante = '. ($ultimoComprobante->getFechaAnulacion()? $ultimoComprobante->getFechaAnulacion()->format('d/m/Y')  : '') .'<br>';
//                echo '<hr><hr><hr>';

                    if ($comprobante->getId() == $ultimoComprobante->getId() ||
                            (!($comprobante->isEqualTo($ultimoComprobante) //
                            && $ultimoComprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO //
//                            && $comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO //
                            && ($ultimoComprobante->getFechaAnulacion() != null && $ultimoComprobante->getFechaAnulacion() > $fecha)))) {


                        $comprobante_anulado = $comprobante->getEstaAnuladoALaFecha($fecha);
                        $comprobante_saldo = $comprobante->getSaldoALaFecha($fecha);

                        $comprobante_array = [
                            'id' => $comprobante->getId(),
                            'fecha' => $comprobante->getFechaComprobante(),
                            'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                            'numero' => $comprobante->getNumeroCompleto(),
                            'monto' => $comprobante->getTotal(),
                            'saldo' => $comprobante_saldo,
                            'anulado' => $comprobante_anulado,
                            'pagos_parciales' => array(),
                            'notas_credito' => array()
                        ];

                        if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteObra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                            $comprobante_array['restaSaldo'] = false;
                            if ($comprobante->getEsNotaCredito()) {
                                $comprobante_array['restaSaldo'] = true;
                            }
                        }

                        $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                        $tramos[$tramo->getId()]['saldo'] += $comprobante_array['restaSaldo'] ? $comprobante_saldo * -1 : $comprobante_saldo;

                        /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                        foreach ($comprobante->getPagosParcialesPagosALaFecha($fecha) as $pagoParcial) {
                            $pago_parcial_array = [
                                'id' => $pagoParcial->getId(),
                                'fecha' => $pagoParcial->getFechaPago(),
                                'tipoComprobante' => 'Pago parcial',
                                'numero' => '-',
                                'monto' => $pagoParcial->getImporte(),
                                'saldo' => 0,
                                'anulado' => $comprobante_anulado,
                                'restaSaldo' => true
                            ];

                            $comprobante_array['pagos_parciales'][] = $pago_parcial_array;
                        }

                        /* @var $notaCredito \ADIF\ContableBundle\Entity\NotaCreditoComprobante */
                        foreach ($comprobante->getComprobantesAcreditadosNoAnuladosALaFecha($fecha) as $notaCreditoComprobante) {
                            $notaCredito = $notaCreditoComprobante->getNotaCredito();
                            $montoAcreditado = 0;

                            /* @var $renglonComprobante \ADIF\ContableBundle\Entity\RenglonComprobante */
                            foreach ($notaCredito->getRenglonesComprobante() as $renglonComprobante) {
                                if ($renglonComprobante->getRenglonAcreditado()->getComprobante()->getId() == $comprobante->getId()) {

                                    $montoAcreditado += $renglonComprobante->getMontoBruto();
                                }
                            }

                            if ($montoAcreditado > 0) {
                                $nota_credito_array = [
                                    'id' => $notaCredito->getId(),
                                    'fecha' => $notaCredito->getFechaComprobante(),
                                    'tipoComprobante' => $notaCredito->getTipoComprobante()->getNombre() . (($notaCredito->getLetraComprobante() != null) ? ' (' . $notaCredito->getLetraComprobante()->getLetra() . ')' : ''),
                                    'numero' => $notaCredito->getNumeroCompleto(),
                                    'monto' => $montoAcreditado,
                                    'saldo' => 0,
                                    'anulado' => false,
                                    'restaSaldo' => true
                                ];

                                $comprobante_array['notas_credito'][] = $nota_credito_array;
                            }
                        }

                        if ($comprobante->getOrdenPago() != null) {
                            $op = $comprobante->getOrdenPago();

                            if ($op->getNumeroOrdenPago() != null && $op->getFechaContable() <= $fecha) {
                                if (!isset($tramos[$tramo->getId()]['ordenes_pago'][$op->getId()])) {
                                    $tramos[$tramo->getId()]['ordenes_pago'][$op->getId()] = [
                                        'id' => $op->getId(),
                                        'fecha' => $op->getFechaOrdenPago(),
                                        'tipoComprobante' => 'Orden de pago',
                                        'numero' => $op->getNumeroOrdenPago(),
                                        'monto' => $op->getTotalBruto(),
                                        'saldo' => 0,
                                        'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                        'restaSaldo' => true,
                                        'comprobantes' => array(),
                                        'anticipos' => array()
                                    ];
                                }

                                $tramos[$tramo->getId()]['ordenes_pago'][$op->getId()]['comprobantes'][] = $comprobante_array;

                                if ($op->getAnticipos() != null) {
                                    $anticipos = $op->getAnticipos();
                                    foreach ($anticipos as $anticipo) {
                                        $anticipo_array = [
                                            'id' => $anticipo->getId(),
                                            'fecha' => $anticipo->getFecha(),
                                            'tipoComprobante' => 'Anticipo',
                                            'numero' => '-',
                                            'monto' => $anticipo->getMonto(),
                                            'saldo' => 0,
                                            'anulado' => $op->getEstaAnuladoALaFecha($fecha),
                                            'restaSaldo' => true
                                        ];

                                        $tramos[$tramo->getId()]['ordenes_pago'][$op->getId()]['anticipos'][] = $anticipo_array;
                                    }
                                }
                                /*
                                  if (!isset($tramos[$tramo->getId()]['ordenes_pago'][$orden_pago_array['id']])) {
                                  $tramos[$tramo->getId()]['ordenes_pago'][$orden_pago_array['id']] = $orden_pago_array;
                                  }
                                 */
                            } else {
                                $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                            }
                        } else {
                            $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                        }
                    }

                    $ultimoComprobante = $comprobante;
                }

                $saldoTotal += $tramos[$tramo->getId()]['saldo'];
            }

            //anticipos no cancelados sin oc
            $anticiposNoAplicadosSinOC = $em_contable->getRepository('ADIFContableBundle:AnticipoProveedor')
                    ->createQueryBuilder('ap')
                    ->innerJoin('ap.ordenPago', 'opAnticipo')
                    ->leftJoin('ADIFContableBundle:OrdenPago', 'opCancelacion', Join::WITH, 'ap.ordenPagoCancelada = opCancelacion.id AND (NOT (opCancelacion.fechaContable <= :fecha AND (opCancelacion.fechaAnulacion IS NULL OR opCancelacion.fechaAnulacion > :fecha)))')
                    ->where('ap.idProveedor = :idProv')
                    ->andWhere('opAnticipo.fechaContable <= :fecha AND (opAnticipo.fechaAnulacion IS NULL OR opAnticipo.fechaAnulacion > :fecha)')
                    ->andWhere('ap.ordenPagoCancelada IS NULL')
                    ->setParameter('idProv', $proveedor->getId())
                    ->setParameter('fecha', $fecha->format('Y-m-d 23:59:59'))
                    ->getQuery()
                    ->getResult();

            $anticiposSinOC = array();
            $saldoAnticiposSinOC = 0;

            foreach ($anticiposNoAplicadosSinOC as $anticipo) {
                $saldo_anticipo = $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha) ? 0 : $anticipo->getMonto();
                $anticiposSinOC[$anticipo->getId()] = [
                    'id' => $anticipo->getId(),
                    'fecha' => $anticipo->getFecha(),
                    'tipoComprobante' => 'Anticipo',
                    'numero' => '-',
                    'monto' => $anticipo->getMonto(),
                    'saldo' => $saldo_anticipo,
                    'anulado' => $anticipo->getOrdenPago()->getEstaAnuladoALaFecha($fecha),
                    'restaSaldo' => true
                ];
                $saldoAnticiposSinOC -= $saldo_anticipo;
            }

            $saldoTotal += $saldoAnticiposSinOC;

            $saldos_proveedores[] = array(
                'proveedor' => $proveedor,
                'saldo' => $saldoTotal
            );
        }

        return $this->render('ADIFComprasBundle:Proveedor:filtro_cuenta_corriente_detalle_total_saldo.html.twig', array(
                    'proveedores' => $saldos_proveedores
                        )
        );
    }

    /**
     * Reporte resumen de cuenta corriente de proveedor.
     *
     * @Route("/resumen_cuenta_corriente/", name="proveedor_resumen_cuenta_corriente")
     * @Method("GET|POST")
     * 
     */
    public function reporteResumenCuentaCorrienteAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Proveedor'] = $this->generateUrl('proveedor');
        $bread['Resumen cuenta corriente proveedor'] = null;

        return $this->render('ADIFComprasBundle:Proveedor:reporte.resumen_cuenta_corriente.html.twig', array(
                    //'proveedores' => $proveedores,
                    'breadcrumbs' => $bread,
                    'page_title' => 'Proveedor | Resumen cuenta corriente',
                    'page_info' => 'Resumen cuenta corriente proveedor'
        ));
    }

    /**
     * Tabla para reporte resumen de cuenta corriente .
     *
     * @Route("/index_table_reporte_resumen_cuenta_corriente/", name="index_table_proveedor_reporte_resumen_cuenta_corriente")
     * @Method("GET|POST")
     */
    public function indexTableReporteResumenCuetaCorrienteAction(Request $request) {
//        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
//        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
//
//        $fechaRequest = $request->query->get('fechaFin');
//        $fecha = $fechaRequest == null ? (new \DateTime()) : \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');
//
//        $proveedores = [];
//
//        $tramos = $emContable->getRepository('ADIFContableBundle:Obras\Tramo')->findAll();
//
//        /* @var $tramo \ADIF\ContableBundle\Entity\Obras\Tramo */
//        foreach ($tramos as $tramo) {
//
//            /* @var $proveedor Proveedor */
//
//            $proveedor = $tramo->getProveedor();
//            $saldo = $this->saldoTramo($emContable, $tramo, $fecha);
//
//            if (!isset($proveedores[$proveedor->getId()])) {
//
//                $proveedores[$proveedor->getId()] = array(
//                    'id' => $proveedor->getId(),
//                    'codigo' => $proveedor->getClienteProveedor()->getCodigo(),
//                    'razonSocial' => $proveedor->getRazonSocial(),
//                    'cuit' => $proveedor->getCUIT(),
//                    'tipoContratacion' => 'Tramo de obra',
//                    'numero' => $tramo->__toString(),
//                    'cuentaContable' => $proveedor->getCuentaContable(),
//                    'saldoPendientePago' => $saldo,
//                    'muestraDetalle' => false
//                );
//            } else {
//
//                $linkVerDetalle = '<a data-id-proveedor=\"' . $proveedor->getId() . '\" class=\"tooltips link-detalle-saldo\" data-original-title=\"Ver detalle\">Ver detalle</a>';
//
//                $proveedores[$proveedor->getId()]['tipoContratacion'] = $linkVerDetalle;
//
//                $proveedores[$proveedor->getId()]['numero'] = $linkVerDetalle;
//
//                $proveedores[$proveedor->getId()]['saldoPendientePago'] += $saldo;
//
//                $proveedores[$proveedor->getId()]['muestraDetalle'] = true;
//            }
//        }
//
//        $ocs = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')//->findAll(); //ordenes de compra con idcompra orginal distinta de nul ordencompra de servicio
//                ->createQueryBuilder('oc')
//                ->where('oc.ordenCompraOriginal IS NOT NULL')
//                ->orWhere('oc.ordenCompraOriginal IS NULL and oc.cotizacion IS NULL')
//                ->getQuery()
//                ->getResult();
//
//        /* @var $oc \ADIF\ComprasBundle\Entity\OrdenCompra */
//
//        foreach ($ocs as $oc) {
//
//            /* @var $proveedor Proveedor */
//
//            $proveedor = $oc->getProveedor();
//            $saldo = $this->saldoOC($emContable, $oc, $fecha);
//
//            if (!isset($proveedores[$proveedor->getId()])) {
//
//                $proveedores[$proveedor->getId()] = array(
//                    'id' => $proveedor->getId(),
//                    'codigo' => $proveedor->getClienteProveedor()->getCodigo(),
//                    'razonSocial' => $proveedor->getRazonSocial(),
//                    'cuit' => $proveedor->getCUIT(),
//                    'tipoContratacion' => 'Orden de compra',
//                    'numero' => $oc->getNumeroOrdenCompra(),
//                    'cuentaContable' => $proveedor->getCuentaContable(),
//                    'saldoPendientePago' => $saldo,
//                    'muestraDetalle' => false
//                );
//            } else {
//
//                $linkVerDetalle = '<a data-id-proveedor=\"' . $proveedor->getId() . '\" class=\"tooltips link-detalle-saldo\" data-original-title=\"Ver detalle\">Ver detalle</a>';
//
//                $proveedores[$proveedor->getId()]['tipoContratacion'] = $linkVerDetalle;
//
//                $proveedores[$proveedor->getId()]['numero'] = $linkVerDetalle;
//
//                $proveedores[$proveedor->getId()]['saldoPendientePago'] += $saldo;
//
//                $proveedores[$proveedor->getId()]['muestraDetalle'] = true;
//            }
//        }
//
//        return $this->render('ADIFComprasBundle:Proveedor:index_table_reporte.resumen_cuenta_corriente.html.twig', array(
//                    'proveedores' => $proveedores,
//        ));

        $fechaRequest = $request->query->get('fechaFin');
        $fecha = $fechaRequest == null ? (new \DateTime()) : \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('codigo', 'codigo');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('tipoContratacion', 'tipoContratacion');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('cuentaContable', 'cuentaContable');
        $rsm->addScalarResult('saldo', 'saldoPendientePago');
        $rsm->addScalarResult('numero', 'numero');

//        $native_query = $em->createNativeQuery('
//            SELECT
//            *
//            FROM
//                vistaresumencuentacorrienteproveedores
//        ', $rsm);
        $native_query = $em->createNativeQuery('
            call sp_vista_resumencuentacorrienteproveedores(?)
        ', $rsm);

        $native_query->setParameter(1, $fecha, Type::DATETIME);

        $proveedoresSaldos = $native_query->getResult();

        $proveedores = [];


        foreach ($proveedoresSaldos as $proveedor) {

//            $muestraDetalle = $proveedor['tipoContratacion'] == null ? true : false;
//            $linkVerDetalle = '<a data-id-proveedor=\"' . $proveedor['id'] . '\" class=\"tooltips link-detalle-saldo\" data-original-title=\"Ver detalle\">Ver detalle</a>';

            $proveedores[$proveedor['id']] = array(
                'id' => $proveedor['id'],
                'codigo' => $proveedor['codigo'],
                'razonSocial' => $proveedor['razonSocial'],
                'cuit' => $proveedor['cuit'],
//                    'tipoContratacion' => $muestraDetalle ? $linkVerDetalle : $proveedor['tipoContratacion'],
//                    'numero' => $muestraDetalle ? $linkVerDetalle : $proveedor['numero'],
                'tipoContratacion' => $proveedor['tipoContratacion'],
                'numero' => $proveedor['numero'],
                'cuentaContable' => $proveedor['cuentaContable'],
                'saldoPendientePago' => $proveedor['saldoPendientePago'],
//                    'muestraDetalle' => $muestraDetalle
                'muestraDetalle' => false
            );
        }

        return $this->render('ADIFComprasBundle:Proveedor:index_table_reporte.resumen_cuenta_corriente.html.twig', array(
                    'proveedores' => $proveedores,
        ));
    }

    /**
     * 
     * Detalle proveedor
     * 
     * @Route("/detalle_resumen_cuenta_corriente/", name="proveedor_detalle_resumen_cuenta_corriente")
     * @Method("GET|POST")
     */
    public function detalleResumenCuentaCorrienteProveedorAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $fechaRequest = $request->query->get('fechaFin');
        $fecha = $fechaRequest == null ? (new \DateTime()) : \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');

        $contratosJson = [];


        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($request->request->get('id_proveedor'));

        $tramos = $emContable->getRepository('ADIFContableBundle:Obras\Tramo')->findByIdProveedor($request->request->get('id_proveedor'));

        /* @var $oc \ADIF\ComprasBundle\Entity\OrdenCompra */

        foreach ($tramos as $tramo) {

            $contratosJson[] = [
                'id' => $tramo->getId(),
                'tipoContratacion' => 'Tramo de obra',
                'numero' => $tramo->__toString(),
                'saldoPendientePago' => $this->saldoTramo($emContable, $tramo, $fecha),
            ];
        }

        $ocs = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')->findByProveedor($proveedor);

        /* @var $oc \ADIF\ComprasBundle\Entity\OrdenCompra */

        foreach ($ocs as $oc) {

            $contratosJson[] = [
                'id' => $oc->getId(),
                'tipoContratacion' => 'Orden de compra',
                'numero' => $oc->getNumeroOrdenCompra(),
                'saldoPendientePago' => $this->saldoOC($emContable, $oc, $fecha)
            ];
        }

        return new JsonResponse($contratosJson);
    }

    private function saldoOC($em_contable, $oc, $fecha) {

        $repository_cc = $em_contable->getRepository('ADIFContableBundle:ComprobanteCompra');

        $tiposComprobantesSumanSaldo = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES
        ];

        $saldo = 0;

        $comprobantes = $repository_cc->getComprobantesCompraByOrdenCompra($oc->getId());

        $anticiposAplicados = array();
        $opAplicadas = array();

        /* @var $comprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
        foreach ($comprobantes as $comprobante) {
            if ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::CUPON &&
                    $comprobante->getFechaContable() <= $fecha) {
                if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                    if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {
                        $saldo += $comprobante->getTotal() *
                                (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesSumanSaldo) ? 1 : -1);
                    }
                }

                if ($comprobante->getOrdenPago() != null && $comprobante->getOrdenPago()->getFechaContable() <= $fecha) {
                    $op = $comprobante->getOrdenPago();
                    if (!in_array($op->getId(), $opAplicadas)) {
                        if (($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) || ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA)) {
                            $monto = $op->getTotalBruto();
                            if ($op->getAnticipos() != null) {
                                $anticipos = $op->getAnticipos();
                                foreach ($anticipos as $anticipo) {
                                    if (!in_array($anticipo->getId(), $anticiposAplicados)) {
                                        //$saldo -= $anticipo->getMonto();
                                        $anticiposAplicados[] = $anticipo->getId();
                                    }
                                }
                            }
                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                //$saldo -= $monto;
                                $saldo = 0;
                            }
                        }
                        $opAplicadas[] = $op->getId();
                    }
                }
            }
        }

        //anticipos no cancelados
        $anticiposNoAplicados = $em_contable->getRepository('ADIFContableBundle:AnticipoOrdenCompra')
                ->createQueryBuilder('a')
                ->where('a.idOrdenCompra = :idOC')
                ->andWhere('a.ordenPagoCancelada IS NULL')
                ->setParameter('idOC', $oc->getId())
                ->getQuery()
                ->getResult();

        foreach ($anticiposNoAplicados as $anticipo) {

            if ($anticipo->getOrdenPago() != null &&
                    $anticipo->getOrdenPago()->getFechaContable() <= $fecha) {

                $saldo -= ($anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA) //
                        ? 0 //
                        : $anticipo->getMonto();
            }
        }
        return $saldo;
    }

    private function saldoTramo($em_contable, $tramo, $fecha) {

        $repository_co = $em_contable->getRepository('ADIFContableBundle:Obras\ComprobanteObra');

        $tiposComprobantesObraSumanSaldo = [
            ConstanteTipoComprobanteObra::FACTURA,
            ConstanteTipoComprobanteObra::NOTA_DEBITO,
            ConstanteTipoComprobanteObra::NOTA_DEBITO_INTERESES,
            ConstanteTipoComprobanteObra::RECIBO,
            ConstanteTipoComprobanteObra::TICKET_FACTURA
        ];

        $saldo = 0;

        /* @var $tramo \ADIF\ContableBundle\Entity\Obras\Tramo */
        $comprobantes = $repository_co->getComprobantesObraByTramo($tramo->getId());

        $anticiposAplicados = array();
        $opAplicadas = array();

        /* @var $comprobante \ADIF\ContableBundle\Entity\Obras\ComprobanteObra */
        foreach ($comprobantes as $comprobante) {
            if ($comprobante->getFechaContable() <= $fecha) {
                if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteObra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                    if ($comprobante->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {
                        $saldo += $comprobante->getTotal() *
                                (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesObraSumanSaldo) ? 1 : -1);
                    }
                }

                if ($comprobante->getOrdenPago() != null) {
                    $op = $comprobante->getOrdenPago();
                    if (!in_array($op->getId(), $opAplicadas) &&
                            $op->getFechaContable() <= $fecha) {
                        if (($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) || ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA)) {
                            $monto = $op->getTotalBruto();
                            if ($op->getAnticipos() != null) {
                                $anticipos = $op->getAnticipos();
                                foreach ($anticipos as $anticipo) {
                                    if (!in_array($anticipo->getId(), $anticiposAplicados)) {
                                        $anticiposAplicados[] = $anticipo->getId();
                                    }
                                }
                            }
                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                $saldo -= $monto;
                            }
                        }
                        $opAplicadas[] = $op->getId();
                    }
                }
            }
        }

        return $saldo;
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/letras_comprobante", name="proveedor_letras_comprobante")
     * @Method("POST")
     */
    function getLetrasComprobante(Request $request) {
        /*
          $em = $this->getDoctrine()->getManager($this->getEntityManager());
          $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

          $idProveedor = $request->get('idProveedor');

          $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')
          ->find($idProveedor);

          if (!$proveedor) {
          throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
          }

          $letrasProveedor = $proveedor->getLetrasComprobante();

          $letrasComprobante = $emContable->getRepository('ADIFContableBundle:LetraComprobante')
          ->getLetrasComprobanteByDenominacion($letrasProveedor);

          return new JsonResponse($letrasComprobante); */

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $letrasComprobante = $emContable->getRepository('ADIFContableBundle:LetraComprobante')
                ->createQueryBuilder('l')
                ->select('l.id', 'l.letra')
                ->getQuery();

        return new JsonResponse($letrasComprobante->getResult());
    }

    /**
     * Muestra la cuenta corriente del proveeedor.
     *
     * @Route("/cuentacorrientedetalleproveedoressaldo/", name="proveedor_cta_cte_detalle_proveedores_saldo")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:cuenta_corriente_detalle_proveedores_saldo.html.twig")
     */
    public function cuentaCorrienteDetalleProveedoresSaldoIndexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $proveedores = $em->getRepository('ADIFComprasBundle:Proveedor')
                ->createQueryBuilder('p')
                ->getQuery()
                ->getResult();


        $resultado = array();

        foreach ($proveedores as $proveedor) {
            /* @var $proveedor Proveedor */

//        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

            if (!$proveedor) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
            }

            $ordenesCompra = array();
            $tramos = array();
            $saldoTotal = 0;

            //COMPRAS
            $repository_cc = $em_contable->getRepository('ADIFContableBundle:ComprobanteCompra');

            $tiposComprobantesSumanSaldo = [
                ConstanteTipoComprobanteCompra::FACTURA,
                ConstanteTipoComprobanteCompra::NOTA_DEBITO,
                ConstanteTipoComprobanteCompra::RECIBO,
                ConstanteTipoComprobanteCompra::TICKET_FACTURA,
                ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES
            ];

            foreach ($proveedor->getOrdenesCompraFinal() as $oc) {
                $ordenesCompra[$oc->getId()] = [
                    'id' => $oc->getId(),
                    'saldo' => 0,
                    'nombre' => $oc->__toString(),
                    'total' => $oc->getMonto(),
                    'restante' => $this->get('adif.orden_compra_service')->getSaldoOrdenCompra($oc),
                    'comprobantes' => array()
                ];

                $comprobantes = $repository_cc->getComprobantesCompraByOrdenCompra($oc->getId());

                $anticiposAplicados = array();
                $opAplicadas = array();


                /* @var $comprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
                foreach ($comprobantes as $comprobante) {
                    if ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::CUPON) {
                        $comprobante_array = [
                            'id' => $comprobante->getId(),
                            'fecha' => $comprobante->getFechaComprobante(),
                            'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                            'numero' => $comprobante->getNumeroCompleto(),
                            'monto' => $comprobante->getTotal(),
                            'saldo' => ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) ? 0 : $comprobante->getTotal() *
                                    (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesSumanSaldo) ? 1 : -1),
                            'anulado' => $comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO,
                            'restaSaldo' => false
                        ];
                        if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                            $comprobante_array['restaSaldo'] = false;
                            if ($comprobante->getEsNotaCredito()) {
                                $comprobante_array['restaSaldo'] = true;
                            }
                        }

                        $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;

                        $indexComprobante = sizeof($ordenesCompra[$oc->getId()]['comprobantes']) - 1;

                        /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                        foreach ($comprobante->getPagosParciales() as $pagoParcial) {
                            if ($pagoParcial->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                $comprobante_array = [
                                    'id' => $pagoParcial->getId(),
                                    'fecha' => $pagoParcial->getFechaPago(),
                                    'tipoComprobante' => 'Pago parcial',
                                    'numero' => '-',
                                    'monto' => $pagoParcial->getImporte(),
                                    'saldo' => 0,
                                    'anulado' => false,
                                    'restaSaldo' => true
                                ];

                                $ordenesCompra[$oc->getId()]['comprobantes'][$indexComprobante]['saldo'] -= $pagoParcial->getImporte();

                                $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                            }
                        }

                        if ($comprobante->getOrdenPago() != null) {
                            $op = $comprobante->getOrdenPago();
                            if (!in_array($op->getId(), $opAplicadas)) {
                                if (($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) || ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA)) {
                                    $monto = $op->getTotalBruto();
                                    if ($op->getAnticipos() != null) {
                                        $anticipos = $op->getAnticipos();
                                        foreach ($anticipos as $anticipo) {
                                            if (!in_array($anticipo->getId(), $anticiposAplicados)) {
                                                $comprobante_array = [
                                                    'id' => $anticipo->getId(),
                                                    'fecha' => $anticipo->getFecha(),
                                                    'tipoComprobante' => 'Anticipo',
                                                    'numero' => '-',
                                                    'monto' => $anticipo->getMonto(),
                                                    'saldo' => 0,
                                                    'anulado' => $op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                                                    'restaSaldo' => true
                                                ];
                                                $anticiposAplicados[] = $anticipo->getId();
                                                $monto -= $anticipo->getMonto();
                                                if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                                    $ordenesCompra[$oc->getId()]['comprobantes'][$indexComprobante]['saldo'] -= $anticipo->getMonto();
                                                }
                                                $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                                            }
                                        }
                                    }
                                    if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                        $ordenesCompra[$oc->getId()]['comprobantes'][$indexComprobante]['saldo'] = 0;
                                    }
                                    $comprobante_array = [
                                        'id' => $op->getId(),
                                        'fecha' => $op->getFechaOrdenPago(),
                                        'tipoComprobante' => 'Orden de pago',
                                        'numero' => $op->getNumeroOrdenPago(),
                                        'monto' => $monto,
                                        'saldo' => 0,
                                        'anulado' => $op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                                        'restaSaldo' => true
                                    ];
                                    $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                                }
                                if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                    $opAplicadas[] = $op->getId();
                                }
                            } else {
                                if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                    $ordenesCompra[$oc->getId()]['comprobantes'][$indexComprobante]['saldo'] = 0;
                                }
                            }
                        }
                    }
                }

                //anticipos no cancelados con oc
                $anticiposNoAplicados = $em_contable->getRepository('ADIFContableBundle:AnticipoOrdenCompra')
                        ->createQueryBuilder('a')
                        ->where('a.idOrdenCompra = :idOC')
                        ->andWhere('a.ordenPagoCancelada IS NULL')
                        ->setParameter('idOC', $oc->getId())
                        ->getQuery()
                        ->getResult();

                foreach ($anticiposNoAplicados as $anticipo) {
                    $comprobante_array = [
                        'id' => $anticipo->getId(),
                        'fecha' => $anticipo->getFecha(),
                        'tipoComprobante' => 'Anticipo',
                        'numero' => '-',
                        'monto' => $anticipo->getMonto(),
                        'saldo' => ($anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA) ? 0 : $anticipo->getMonto(),
                        'anulado' => $anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                        'restaSaldo' => true
                    ];
                    $ordenesCompra[$oc->getId()]['comprobantes'][] = $comprobante_array;
                }

                foreach ($ordenesCompra[$oc->getId()]['comprobantes'] as $comprobantesCC) {
                    $ordenesCompra[$oc->getId()]['saldo'] += $comprobantesCC['saldo'];
                }

                $saldoTotal += $ordenesCompra[$oc->getId()]['saldo'];
            }

            //OBRAS 
            $repository_co = $em_contable->getRepository('ADIFContableBundle:Obras\ComprobanteObra');

            $tiposComprobantesObraSumanSaldo = [
                ConstanteTipoComprobanteObra::FACTURA,
                ConstanteTipoComprobanteObra::NOTA_DEBITO,
                ConstanteTipoComprobanteObra::NOTA_DEBITO_INTERESES,
                ConstanteTipoComprobanteObra::RECIBO,
                ConstanteTipoComprobanteObra::TICKET_FACTURA
            ];

            $tramosProveedor = $em_contable->getRepository('ADIFContableBundle:Obras\Tramo')->findByIdProveedor($proveedor->getId());

            foreach ($tramosProveedor as $tramo) {
                /* @var $tramo \ADIF\ContableBundle\Entity\Obras\Tramo */
                $tramos[$tramo->getId()] = [
                    'id' => $tramo->getId(),
                    'saldo' => 0,
                    'nombre' => $tramo->__toString(),
                    'total' => $tramo->getTotalContrato(true),
                    'restante' => $tramo->getSaldoFacturable(),
                    'comprobantes' => array()
                ];

                $comprobantes = $repository_co->getComprobantesObraByTramo($tramo->getId());

                $anticiposAplicados = array();
                $opAplicadas = array();

                /* @var $comprobante \ADIF\ContableBundle\Entity\Obras\ComprobanteObra */
                foreach ($comprobantes as $comprobante) {

                    $comprobante_array = [
                        'id' => $comprobante->getId(),
                        'fecha' => $comprobante->getFechaComprobante(),
                        'tipoComprobante' => $comprobante->getTipoComprobante()->getNombre() . (($comprobante->getLetraComprobante() != null) ? ' (' . $comprobante->getLetraComprobante()->getLetra() . ')' : ''),
                        'numero' => $comprobante->getNumeroCompleto(),
                        'monto' => $comprobante->getTotal(),
                        'saldo' => ($comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO) ? 0 : $comprobante->getTotal() *
                                (in_array($comprobante->getTipoComprobante()->getId(), $tiposComprobantesObraSumanSaldo) ? 1 : -1),
                        'anulado' => $comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO
                    ];
                    if (($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteObra::RECIBO) || (($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::RECIBO) && ($comprobante->getLetraComprobante() == ConstanteLetraComprobante::C))) {
                        $comprobante_array['restaSaldo'] = false;
                        if ($comprobante->getEsNotaCredito()) {
                            $comprobante_array['restaSaldo'] = true;
                        }
                    }

                    $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;

                    $indexComprobante = sizeof($tramos[$tramo->getId()]['comprobantes']) - 1;

                    /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                    foreach ($comprobante->getPagosParciales() as $pagoParcial) {
                        if ($pagoParcial->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                            $comprobante_array = [
                                'id' => $pagoParcial->getId(),
                                'fecha' => $pagoParcial->getFechaPago(),
                                'tipoComprobante' => 'Pago parcial',
                                'numero' => '-',
                                'monto' => $pagoParcial->getImporte(),
                                'saldo' => 0,
                                'anulado' => false,
                                'restaSaldo' => true
                            ];

                            $tramos[$tramo->getId()]['comprobantes'][$indexComprobante]['saldo'] -= $pagoParcial->getImporte();

                            $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                        }
                    }

                    if ($comprobante->getOrdenPago() != null) {
                        $op = $comprobante->getOrdenPago();
                        if (!in_array($op->getId(), $opAplicadas)) {
                            if (($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) || ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA)) {
                                $monto = $op->getTotalBruto();
                                if ($op->getAnticipos() != null) {
                                    $anticipos = $op->getAnticipos();
                                    foreach ($anticipos as $anticipo) {
                                        if (!in_array($anticipo->getId(), $anticiposAplicados)) {
                                            $comprobante_array = [
                                                'id' => $anticipo->getId(),
                                                'fecha' => $anticipo->getFecha(),
                                                'tipoComprobante' => 'Anticipo',
                                                'numero' => '-',
                                                'monto' => $anticipo->getMonto(),
                                                'saldo' => 0,
                                                'anulado' => $op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                                                'restaSaldo' => true
                                            ];
                                            $anticiposAplicados[] = $anticipo->getId();
                                            $monto -= $anticipo->getMonto();
                                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                                $tramos[$tramo->getId()]['comprobantes'][$indexComprobante]['saldo'] -= $anticipo->getMonto();
                                            }
                                            $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                                        }
                                    }
                                }
                                if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                    $tramos[$tramo->getId()]['comprobantes'][$indexComprobante]['saldo'] = 0;
                                }
                                $comprobante_array = [
                                    'id' => $op->getId(),
                                    'fecha' => $op->getFechaOrdenPago(),
                                    'tipoComprobante' => 'Orden de pago',
                                    'numero' => $op->getNumeroOrdenPago(),
                                    'monto' => $monto,
                                    'saldo' => 0,
                                    'anulado' => $op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                                    'restaSaldo' => true
                                ];
                                $tramos[$tramo->getId()]['comprobantes'][] = $comprobante_array;
                            }
                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                $opAplicadas[] = $op->getId();
                            }
                        } else {
                            if ($op->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                                $tramos[$tramo->getId()]['comprobantes'][$indexComprobante]['saldo'] = 0;
                            }
                        }
                    }
                }


                foreach ($tramos[$tramo->getId()]['comprobantes'] as $comprobantesCC) {
                    $tramos[$tramo->getId()]['saldo'] += $comprobantesCC['saldo'];
                }

                $saldoTotal += $tramos[$tramo->getId()]['saldo'];
            }

            //anticipos no cancelados sin oc
            $anticiposNoAplicadosSinOC = $em_contable->getRepository('ADIFContableBundle:AnticipoProveedor')
                    ->createQueryBuilder('ap')
//                ->innerJoin('a.anticipo', 'a')
                    ->where('ap.idProveedor = :idProv')
                    ->andWhere('ap.ordenPagoCancelada IS NULL')
//                ->andWhere('a.ordenCompraOriginal IS NULL')
                    ->setParameter('idProv', $proveedor->getId())
                    ->getQuery()
                    ->getResult();

            $saldoAnticiposSinOC = 0;
            $anticiposSinOC = array();
            foreach ($anticiposNoAplicadosSinOC as $anticipo) {
                $saldo = ($anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA) ? 0 : $anticipo->getMonto();
                $anticiposSinOC[$anticipo->getId()] = [
                    'id' => $anticipo->getId(),
                    'fecha' => $anticipo->getFecha(),
                    'tipoComprobante' => 'Anticipo',
                    'numero' => '-',
                    'monto' => $anticipo->getMonto(),
                    'saldo' => $saldo,
                    'anulado' => $anticipo->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA,
                    'restaSaldo' => true
                ];
                $saldoAnticiposSinOC += $saldo;
            }

            $saldoTotal -= $saldoAnticiposSinOC;

            $resultado[] = [
                'proveedor' => $proveedor,
                'saldo' => $saldoTotal
            ];
        }

        return array(
            'resultados' => $resultado,
            'page_title' => 'Proveedores | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Reporte vencimiento proveedores.
     *
     * @Route("/reporte_vencimiento/", name="proveedor_reporte_vencimiento")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:reporte_vencimiento.html.twig")
     * 
     */
    public function reporteVencimientoProveedoresAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte vencimiento proveedores'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Reporte vencimiento proveedores'
        );
    }

    /**
     * Reporte vencimiento proveedores.
     *
     * @Route("/index_table_reporte_vencimiento/", name="proveedor_index_table_reporte_vencimiento")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:index_table_reporte.vencimiento.html.twig")
     * 
     */
    public function indexTableReporteVencimientoAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('numeroDocumento', 'numeroDocumento');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('tipoProveedor', 'tipoProveedor');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('numeroComprobante', 'numeroComprobante'); 
        $rsm->addScalarResult('numero_referencia', 'numero_referencia');
        $rsm->addScalarResult('importe', 'importe');
        $rsm->addScalarResult('fechaComprobante', 'fechaComprobante');
        $rsm->addScalarResult('fechaIngresoADIF', 'fechaIngresoADIF');
        $rsm->addScalarResult('fechaVencimientoComprobante', 'fechaVencimientoComprobante');
        $rsm->addScalarResult('fechaVencimientoComprobanteReal', 'fechaVencimientoComprobanteReal');
        $rsm->addScalarResult('plazoPrevistoPago', 'plazoPrevistoPago');
        $rsm->addScalarResult('estaVencida', 'estaVencida');
        $rsm->addScalarResult('diasDeVencimiento', 'diasDeVencimiento');

        $sql = "CALL sp_reporte_vencimiento_comprobantes()";

        $nativeQuery = $em->createNativeQuery($sql, $rsm);

        $comprobantes = $nativeQuery->getResult();

        return array(
            'comprobantes' => $comprobantes
        );
    }

    /**
     * Reporte vencimiento comprobante.
     *
     * @Route("/index_table_reporte_resumen_aging/", name="proveedor_index_table_reporte_resumen_aging")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:index_table_reporte.resumen_aging.html.twig")
     * 
     */
    public function indexTableResumenAgingReporteVencimientoComprobanteAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult("<60", "< 60");

        /*
        $rsm->addScalarResult("<60", "< 60");
        $rsm->addScalarResult("<45", "< 45");
        $rsm->addScalarResult("<35", "< 35");
        $rsm->addScalarResult("<25", "< 25");
        $rsm->addScalarResult("<15", "< 15");
        $rsm->addScalarResult("<10", "< 10");
        $rsm->addScalarResult("<0", "< 0");
        $rsm->addScalarResult(">0", "> 0");
        $rsm->addScalarResult(">5", "> 5");
        $rsm->addScalarResult(">10", "> 10");
        $rsm->addScalarResult(">15", "> 15");
        $rsm->addScalarResult(">25", "> 25");
        $rsm->addScalarResult(">35", "> 35");
        $rsm->addScalarResult(">45", "> 45");
        $rsm->addScalarResult(">60", "> 60");
        */
        
        $rsm->addScalarResult("Menor60", "< 60");
        $rsm->addScalarResult("Menor45", "< 45");
        $rsm->addScalarResult("Menor35", "< 35");
        $rsm->addScalarResult("Menor25", "< 25");
        $rsm->addScalarResult("Menor15", "< 15");
        $rsm->addScalarResult("Menor10", "< 10");
        $rsm->addScalarResult("Menor0", "< 0");
        $rsm->addScalarResult("Mayor0", "> 0");
        $rsm->addScalarResult("Mayor5", "> 5");
        $rsm->addScalarResult("Mayor10", "> 10");
        $rsm->addScalarResult("Mayor15", "> 15");
        $rsm->addScalarResult("Mayor25", "> 25");
        $rsm->addScalarResult("Mayor35", "> 35");
        $rsm->addScalarResult("Mayor45", "> 45");
        $rsm->addScalarResult("Mayor60", "> 60");
        $rsm->addScalarResult("SinVto", "SinVto");
        $rsm->addScalarResult("suma_total", "suma_total");

        $sql = "CALL sp_reporte_vencimiento_resumen_aging()";

        $nativeQuery = $em->createNativeQuery($sql, $rsm);
        
        $resumenAging = $nativeQuery->getSingleResult();

        foreach ($resumenAging as $rango => $sumatoria) {

            $orden = str_replace("< ", "-", str_replace(">", "", $rango));

            $resultadoResumenAging[] = [
                'rango' => $rango,
                'orden' => $orden,
                'sumatoria' => $sumatoria
            ];
        }

        return array(
            'resumenAging' => $resultadoResumenAging
        );
    }
	
	/**
	* Me devuelve las partidas abiertas que tenga el proveedor de compra/obra
	* @Route("/get_partidas_abiertas_proveedor")
	*/
	public function getPartidasAbiertasByIdProveedor(Request $request)
	{
		$idProveedor = $request->get('id');
		
		if ($idProveedor == null) {
			return new JsonResponse(array());
		}
		
		$em = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

                $rsm = new ResultSetMapping();
                $rsm->addScalarResult('idComprobante', 'idComprobante');
                $rsm->addScalarResult('fechaContable', 'fechaContable');
                $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
                $rsm->addScalarResult('letra', 'letra');
                $rsm->addScalarResult('puntoVenta', 'puntoVenta');
                $rsm->addScalarResult('numero', 'numero');
                //$rsm->addScalarResult('saldo', 'saldo');
                $rsm->addScalarResult('total', 'total');
                $rsm->addScalarResult('estado', 'estado');

                $nativeQuery = $em->createNativeQuery('CALL sp_get_partidas_abiertas_proveedor(?)', $rsm);
		$nativeQuery->setParameter(1, $idProveedor);
		
		$comprobantes = $nativeQuery->getResult();
		
		for($i = 0; $i < count($comprobantes); $i++){
			$comprobante = $em->getRepository('ADIFContableBundle:Comprobante')->find($comprobantes[$i]['idComprobante']);
			$comprobantes[$i]['total'] = number_format($comprobantes[$i]['total'], 2, ',', '.');
			$comprobantes[$i]['saldo'] = number_format($comprobante->getSaldoALaFecha(new \DateTime()), 2, ',', '.');
		}
		
		return new JsonResponse($comprobantes);
	}
    
    ####################
    ##### PADRONES #####
    ####################
    
    /**
     * Pagina index de padrones, donde lista los padrones cargados.
     *
     * @Route("/padrones/", name="padrones")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:index.padrones.html.twig")
     */
    public function padronAction() 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entities = $em->getRepository('ADIFComprasBundle:Padron')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Padrones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Padrones de excencion de impuestos',
            'page_info' => 'Padrones de excencion de impuestos',
            'entities' => $entities,
            'padronPendiente' => null
        );
    }
    
    /**
     * Importa los padrones en formato txt, de los tipos de impuesto que pueden ser:
     * IVA REG 2226
     * IVA REG 18
     * IIBB
     * SUSS
     * Ganancias
     * 
     * @var Request $request
     * 
     * @Route("/importarPadron/", name="importar_padron")
     * @Method("POST")
     */
    public function importarPadron(Request $request) 
    {
        $dir = __DIR__ . '/../../../../web/uploads/padrones';
        $filename = $request->request->get('form_importar_padron_impuesto') . '_' . time();
        $ext = '.txt';

        try {
            
            $request->files->all()['form_importar_padron_file']->move($dir, $filename . $ext);

            // Listo todos los proveedores
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
            $entities = $em->getRepository('ADIFComprasBundle:Proveedor')->findAll();

            if (!$entities) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
            }

            // Me traigo todo los proveedores
            $cuits = array();
            $cuitsPadron = array();

            foreach ($entities as $proveedor) {
                $cuits[] = $proveedor->getClienteProveedor()->getCuit();
            }

            // Abro el archivo y busco cada cuit en el listado de cuits de proveedores de adif
            $actualizacion = array();
            $actualizaciones = array();
            $noModificados = '<br />';

            if (($file = fopen($dir . '/' . $filename . '.txt', 'r')) !== FALSE) {

                switch ( $request->request->get('form_importar_padron_impuesto') ) {
                    case ConstanteTipoImpuesto::IVA.'2226':
                        while (($row = fgets($file, 140)) !== FALSE) {

                            $cuit  = trim(substr($row,0,12));
                            $cuit1 = substr($cuit,0,2);
                            $cuit2 = substr($cuit,2,8);
                            $cuit3 = substr($cuit,-1,1);
                            $cuit  = $cuit1.'-'.$cuit2.'-'.$cuit3;
                            $regimen    = trim(substr($row,109,20));
                            $fechaDesde = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', trim(substr($row,74,11))))));
                            $fechaHasta = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', trim(substr($row,85,11))))));
                            $porcentaje = trim(substr($row,95,14));
                            $nroCertificado = 'n/d';
                            $cnr        = trim(substr($row,129));

                            if (in_array($cuit, $cuits) && $cnr != 'CNR_IMPO') {

                                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                                        ->findOneBy(array('CUIT' => $cuit));
                                $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByClienteProveedor($clienteProveedor);

                                if($proveedor == null){
                                    $noModificados .= "La actualizacion del CUIT/Razon social ".$cuit."/".$clienteProveedor->getRazonSocial()." no se realiz&oacute; correctamente (la actualizacion debe realizarse manualmente).<br />Datos de la actualizaci&oacute;n: ".$row;
                                } else {
                                    if ($proveedor->getCertificadoExencionIVA() == null) {
                                        $actualizaciones[] = array( 'exencion' => 'IVA',
                                                                    'proveedor' => $proveedor,
                                                                    'clienteProveedor' => $clienteProveedor,
                                                                    'cuit' => $cuit,
                                                                    'idCertificado' => null,
                                                                    'regimen' => $regimen,
                                                                    'porcentaje' => $porcentaje,
                                                                    'fechaDesde' => $fechaDesde,
                                                                    'fechaHasta' => $fechaHasta,
                                                                    'nroCertificado' =>$nroCertificado);
                                    } else {

                                        $desde = $proveedor->getCertificadoExencionIVA()->getFechaDesde()->diff($fechaDesde);
                                        $hasta = $proveedor->getCertificadoExencionIVA()->getFechaHasta()->diff($fechaHasta);

                                        if ($desde->days !== 0 || $hasta->days !== 0 || $desde->invert !== 0 || $hasta->invert !== 0 || (int)$proveedor->getCertificadoExencionIVA()->getPorcentajeExencion() !== (int)$porcentaje) {
                                            $actualizaciones[] = array( 'exencion' => 'IVA',
                                                                        'proveedor' => $proveedor,
                                                                        'clienteProveedor' => $clienteProveedor,
                                                                        'cuit' => $cuit,
                                                                        'idCertificado' => $proveedor->getCertificadoExencionIVA(),
                                                                        'regimen' => $regimen,
                                                                        'porcentaje' => $porcentaje,
                                                                        'fechaDesde' => $fechaDesde,
                                                                        'fechaHasta' => $fechaHasta,
                                                                        'nroCertificado' =>$nroCertificado);
                                        }
                                    }
                                    #$clienteProveedor->setExentoIVA(true);
                                }
                            }
                        }
                    break;
                    case  ConstanteTipoImpuesto::IVA.'18':
                        while (($row = fgets($file, 1024)) !== FALSE) {
                            $empadronado = explode(';', $row);

                            $cuit  = trim($empadronado[0]);
                            $cuit1 = substr($cuit,0,2);
                            $cuit2 = substr($cuit,2,8);
                            $cuit3 = substr($cuit,-1,1);
                            $cuit  = $cuit1.'-'.$cuit2.'-'.$cuit3;
                            $regimen    = trim($empadronado[4]);
                            $fechaDesde = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $empadronado[2]))));
                            $fechaHasta = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', '31/12/2030'))));
                            $nroCertificado = 'n/d';
                            $porcentaje = 100;

                            if (in_array($cuit, $cuits)) {
                                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                                        ->findOneBy(array('CUIT' => $cuit));
                                $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByClienteProveedor($clienteProveedor);

                                if($proveedor == null){
                                    $noModificados .= "La actualizacion del CUIT/Razon social ".$cuit."/".$clienteProveedor->getRazonSocial()." no se realiz&oacute; correctamente (la actualizacion debe realizarse manualmente).<br />Datos de la actualizaci&oacute;n: ".$row;
                                } else {

                                    if ($proveedor->getCertificadoExencionIVA() == null) {
                                            $actualizaciones[] = array( 'exencion' => 'IVA',
                                                                        'proveedor' => $proveedor,
                                                                        'clienteProveedor' => $clienteProveedor,
                                                                        'cuit' => $cuit,
                                                                        'idCertificado' => null,
                                                                        'regimen' => $regimen,
                                                                        'porcentaje' => $porcentaje,
                                                                        'fechaDesde' => $fechaDesde,
                                                                        'fechaHasta' => $fechaHasta,
                                                                        'nroCertificado' =>$nroCertificado);
                                    } else {

                                        $desde = $proveedor->getCertificadoExencionIVA()->getFechaDesde()->diff($fechaDesde);
                                        $hasta = $proveedor->getCertificadoExencionIVA()->getFechaHasta()->diff($fechaHasta);

                                        if ($desde->days !== 0 || $hasta->days !== 0 || $desde->invert !== 0 || $hasta->invert !== 0 || (int)$proveedor->getCertificadoExencionIVA()->getPorcentajeExencion() !== (int)$porcentaje) {
                                            $actualizaciones[] = array( 'exencion' => 'IVA',
                                                                        'proveedor' => $proveedor,
                                                                        'clienteProveedor' => $clienteProveedor,
                                                                        'cuit' => $cuit,
                                                                        'idCertificado' => $proveedor->getCertificadoExencionIVA(),
                                                                        'regimen' => $regimen,
                                                                        'porcentaje' => $porcentaje,
                                                                        'fechaDesde' => $fechaDesde,
                                                                        'fechaHasta' => $fechaHasta,
                                                                        'nroCertificado' =>$nroCertificado);
                                        }
                                    }
                                    
                                    #if()#$clienteProveedor->setExentoIVA(true);
                                }
                            }
                        }
                    break;
                    case ConstanteTipoImpuesto::IIBB:
                        while (($row = fgets($file, 1024)) !== FALSE) {
                            $empadronado = explode(';', $row);

                            $cuit  = trim($empadronado[3]);
                            $cuit1 = substr($cuit,0,2);
                            $cuit2 = substr($cuit,2,8);
                            $cuit3 = substr($cuit,-1,1);
                            $cuit  = $cuit1.'-'.$cuit2.'-'.$cuit3;
                            $regimen    = 'II.BB.';
                            $fechaDesde = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', substr($empadronado[1],0,2).'/'.substr($empadronado[1],2,2).'/'.substr($empadronado[1],4,4)))));
                            $fechaHasta = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', substr($empadronado[2],0,2).'/'.substr($empadronado[2],2,2).'/'.substr($empadronado[2],4,4)))));
                            $nroCertificado = 'n/d';
                            $porcentaje = $empadronado[8];

                            if (in_array($cuit, $cuits)) {
                                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                                        ->findOneBy(array('CUIT' => $cuit));
                                $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByClienteProveedor($clienteProveedor);

                                if($proveedor == null){
                                        $noModificados .= "La actualizacion del CUIT/Razon social ".$cuit."/".$clienteProveedor->getRazonSocial()." no se realiz&oacute; correctamente (la actualizacion debe realizarse manualmente).<br />Datos de la actualizaci&oacute;n: ".$row;
                                } else {

                                    if ($proveedor->getCertificadoExencionIngresosBrutos() == null) {
                                        $actualizaciones[] = array( 'exencion' => 'IngresosBrutos',
                                                                    'proveedor' => $proveedor,
                                                                    'clienteProveedor' => $clienteProveedor,
                                                                    'cuit' => $cuit,
                                                                    'idCertificado' => null,
                                                                    'regimen' => $regimen,
                                                                    'porcentaje' => $porcentaje,
                                                                    'fechaDesde' => $fechaDesde,
                                                                    'fechaHasta' => $fechaHasta,
                                                                    'nroCertificado' => $nroCertificado);
                                    } else {

                                        $desde = $proveedor->getCertificadoExencionIngresosBrutos()->getFechaDesde()->diff($fechaDesde);
                                        $hasta = $proveedor->getCertificadoExencionIngresosBrutos()->getFechaHasta()->diff($fechaHasta);

                                        if ($desde->days !== 0 || $hasta->days !== 0 || $desde->invert !== 0 || $hasta->invert !== 0 || (int)$proveedor->getCertificadoExencionIngresosBrutos()->getPorcentajeExencion() !== (int)$porcentaje) {
                                            $actualizaciones[] = array( 'exencion' => 'IngresosBrutos',
                                                                        'proveedor' => $proveedor,
                                                                        'clienteProveedor' => $clienteProveedor,
                                                                        'cuit' => $cuit,
                                                                        'idCertificado' => $proveedor->getCertificadoExencionIngresosBrutos(),
                                                                        'regimen' => $regimen,
                                                                        'porcentaje' => $porcentaje,
                                                                        'fechaDesde' => $fechaDesde,
                                                                        'fechaHasta' => $fechaHasta,
                                                                        'nroCertificado' => $nroCertificado);
                                        }
                                    }
                                    #$clienteProveedor->setExentoIngresosBrutos(true);
                                }
                            }
                        }
                    break;
                    case ConstanteTipoImpuesto::SUSS:
                        while (($row = fgets($file, 1024)) !== FALSE) {
                            $empadronado = explode(';', $row);

                            $cuit  = trim($empadronado[0]);
                            $cuit1 = substr($cuit,0,2);
                            $cuit2 = substr($cuit,2,8);
                            $cuit3 = substr($cuit,-1,1);
                            $cuit  = $cuit1.'-'.$cuit2.'-'.$cuit3;
                            $regimen    = $empadronado[4];
                            $fechaDesde = new DateTime(date('Y-m-d', strtotime($empadronado[6])));
                            $fechaHasta = new DateTime(date('Y-m-d', strtotime($empadronado[7])));
                            $nroCertificado = $empadronado[1];
                            $porcentaje = $empadronado[3];

                            if (in_array($cuit, $cuits)) {
                                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')->findOneBy(array('CUIT' => $cuit));
                                $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByClienteProveedor($clienteProveedor);

                                if($proveedor == null){
                                        $noModificados .= "La actualizacion del CUIT/Razon social ".$cuit."/".$clienteProveedor->getRazonSocial()." no se realiz&oacute; correctamente (la actualizacion debe realizarse manualmente).<br />Datos de la actualizaci&oacute;n: ".$row;
                                } else {

                                    if ($proveedor->getCertificadoExencionSUSS() == null) {

                                        $actualizaciones[] = array( 'exencion' => 'SUSS',
                                                                    'proveedor' => $proveedor,
                                                                    'clienteProveedor' => $clienteProveedor,
                                                                    'cuit' => $cuit,
                                                                    'idCertificado' => null,
                                                                    'regimen' => $regimen,
                                                                    'porcentaje' => $porcentaje,
                                                                    'fechaDesde' => $fechaDesde,
                                                                    'fechaHasta' => $fechaHasta,
                                                                    'nroCertificado' => $nroCertificado);
                                    } else {

                                        $desde = $proveedor->getCertificadoExencionSUSS()->getFechaDesde()->diff($fechaDesde);
                                        $hasta = $proveedor->getCertificadoExencionSUSS()->getFechaHasta()->diff($fechaHasta);

                                        if ($desde->days !== 0 || $hasta->days !== 0 || $desde->invert !== 0 || $hasta->invert !== 0 || (int)$proveedor->getCertificadoExencionSUSS()->getPorcentajeExencion() !== (int)$porcentaje) {
                                            $actualizaciones[] = array( 'exencion' => 'SUSS',
                                                                        'proveedor' => $proveedor,
                                                                        'clienteProveedor' => $clienteProveedor,
                                                                        'cuit' => $cuit,
                                                                        'idCertificado' => $proveedor->getCertificadoExencionSUSS(),
                                                                        'regimen' => $regimen,
                                                                        'porcentaje' => $porcentaje,
                                                                        'fechaDesde' => $fechaDesde,
                                                                        'fechaHasta' => $fechaHasta,
                                                                        'nroCertificado' => $nroCertificado);
                                        }
                                    }
                                    #$clienteProveedor->setExentoSUSS(true);
                                }
                            }
                        }
                    break;
                    case ConstanteTipoImpuesto::Ganancias:
                        while (($row = fgets($file, 1024)) !== FALSE) {
                            $empadronado = explode(';', $row);

                            $cuit  = trim($empadronado[1]);
                            $cuit1 = substr($cuit,0,2);
                            $cuit2 = substr($cuit,2,8);
                            $cuit3 = substr($cuit,-1,1);
                            $cuit  = $cuit1.'-'.$cuit2.'-'.$cuit3;
                            $regimen    = 'RG 830';
                            $fechaDesde = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $empadronado[8]))));
                            $fechaHasta = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $empadronado[9]))));
                            $nroCertificado = $empadronado[0];
                            $porcentaje = $empadronado[3];
                            $revocados = $empadronado[5];

                            if (in_array($cuit, $cuits) && $revocados != 'Revocados') {
                                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')->findOneBy(array('CUIT' => $cuit));
                                $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByClienteProveedor($clienteProveedor);

                                if($proveedor == null){

                                    $noModificados .= "La actualizacion del CUIT/Razon social ".$cuit."/".$clienteProveedor->getRazonSocial()." no se realiz&oacute; correctamente (la actualizacion debe realizarse manualmente).<br />Datos de la actualizaci&oacute;n: ".$row;
                                } else {

                                    if ($proveedor->getCertificadoExencionGanancias() == null) {

                                        $actualizaciones[] = array( 'exencion' => 'Ganancias',
                                                                    'proveedor' => $proveedor,
                                                                    'clienteProveedor' => $clienteProveedor,
                                                                    'cuit' => $cuit,
                                                                    'idCertificado' => null,
                                                                    'regimen' => $regimen,
                                                                    'porcentaje' => $porcentaje,
                                                                    'fechaDesde' => $fechaDesde,
                                                                    'fechaHasta' => $fechaHasta,
                                                                    'nroCertificado' => $nroCertificado);

                                    } else {

                                        $desde = $proveedor->getCertificadoExencionGanancias()->getFechaDesde()->diff($fechaDesde);
                                        $hasta = $proveedor->getCertificadoExencionGanancias()->getFechaHasta()->diff($fechaHasta);

                                        if ($desde->days !== 0 || $hasta->days !== 0 || $desde->invert !== 0 || $hasta->invert !== 0 || (int)$proveedor->getCertificadoExencionGanancias()->getPorcentajeExencion() !== (int)$porcentaje) {
                                            $actualizaciones[] = array( 'exencion' => 'Ganancias',
                                                                        'proveedor' => $proveedor,
                                                                        'clienteProveedor' => $clienteProveedor,
                                                                        'cuit' => $cuit,
                                                                        'idCertificado' => $proveedor->getCertificadoExencionGanancias(),
                                                                        'regimen' => $regimen,
                                                                        'porcentaje' => $porcentaje,
                                                                        'fechaDesde' => $fechaDesde,
                                                                        'fechaHasta' => $fechaHasta,
                                                                        'nroCertificado' => $nroCertificado);
                                        }
                                    }
                                    #$clienteProveedor->setExentoGanancias(true);
                                }
                            }
                        }
                    break;
                }

                if ( count($actualizaciones) > 0 ) {

                    $padron = new Padron;
                    $estadoPadron = $em->getRepository('ADIFComprasBundle:EstadoPadron')->find(ConstanteEstadoPadron::ESTADO_PADRON_BORRADOR);

                    $padron->setTipoImpuesto($request->request->get('form_importar_padron_impuesto'));
                    $periodoMes = $request->request->get('form_importar_padron_periodo_mes');
                    $periodoAno = $request->request->get('form_importar_padron_periodo_ano');
                    $periodo = new DateTime(date('Y-m-d', strtotime('01-'.$periodoMes.'-'.$periodoAno)));
                    $padron->setPeriodo($periodo);

                    $padron->setEstadoPadron($estadoPadron);

                    $em->persist($padron);
                    $em->flush();
                    $idPadron = $padron->getId();

                    foreach ($actualizaciones as $key => $value) {
                        
                        $renglonPadron = new RenglonPadron();

                        $renglonPadron->setIdPadron($idPadron);
                        $renglonPadron->setTipoRegimen($value['regimen']);
                        $renglonPadron->setPorcentajeExencion($value['porcentaje']);
                        $renglonPadron->setFechaDesde($value['fechaDesde']);
                        $renglonPadron->setFechaHasta($value['fechaHasta']);
                        $renglonPadron->setNumeroCertificado($value['nroCertificado']);
                        $renglonPadron->setProveedor($value['proveedor']);
                        $renglonPadron->setClienteProveedor($value['clienteProveedor']);
                        $renglonPadron->setCertificadoExencion($value['idCertificado']);
                        $renglonPadron->setActualiza(0);
                        $em->persist($renglonPadron);
                    }
                    
                    $em->flush();
                    
                } else {
                    $this->get('session')->getFlashBag()
                        ->add('warning', "La importaci&oacute;n se realiz&oacute; con &eacute;xito pero no se registran diferencias con la base de datos actual.");
                }

                fclose($file);

            } else {
                $this->get('session')->getFlashBag()
                        ->add('error', "Error al abrir el archivo(.TXT)");
            }

            // Borro el txt
            //unlink($dir . '/' . $filename . $ext);

        } catch (\Exception $e) {
            die($e->getMessage());
            $this->get('session')->getFlashBag()
                    ->add('error', "Se produjeron errores en la importaci&oacute;n.");
        }

        return $this->redirect($this->generateUrl('padrones'));
    }
    

    /**
     * Devuelve el renglon padron por idPadron
     *
     * @Route("/padron/{id}", name="renglonesPadron")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Proveedor:show.renglones_padron.html.twig")
     */
    public function renglonPadronAction($id) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entities = $em->getRepository('ADIFComprasBundle:RenglonPadron')->findByIdPadron($id);
        $padron = $em->getRepository('ADIFComprasBundle:Padron')->find($id);

        $bread = $this->base_breadcrumbs;
        $bread['Padrones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Padrones de excencion de impuestos',
            'page_info' => 'Padrones de excencion de impuestos',
            'entities' => $entities,
            'padron' => $padron
        );
    }
    
    /**
     * Guarda en estado borrados los renglones seleccionados
     * 
     * @Route("/guardarBorradorPadron/{id}", name="guardar_borrador_padron")
     * @Method("POST")
     */
    public function guardarBorradorPadronAction(Request $request, $id, $return = true ) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $seleccionados = $request->request->get("form_guardar_padron_checkbox");
        $rengonesPadron = $em->getRepository('ADIFComprasBundle:RenglonPadron')->findByIdPadron($id);

        foreach ($rengonesPadron as $renglonPadron) {
            if ( in_array($renglonPadron->getID(), $seleccionados) ) {
                $renglonPadron->setActualiza(true);
            } else {
                $renglonPadron->setActualiza(false);
            }

            $em->persist($renglonPadron);
            $em->flush();
        }

        if ($return) {
            return $this->redirect($this->generateUrl('renglonesPadron', array('id' => $id)));
        }
    }

    /**
     * @Route("/guardarPadron/{id}", name="guardar_padron")
     * @Method("POST")
     */
    public function guardarPadronAction(Request $request, $id ) 
    {
        try {
            
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $seleccionados = $this->guardarBorradorPadronAction($request,$id,false);
            $actualizaciones = $em->getRepository('ADIFComprasBundle:RenglonPadron')->findByIdPadron($id);
            //\Doctrine\Common\Util\Debug::dump($actualizaciones);
            //die();
            $padron = $em->getRepository('ADIFComprasBundle:Padron')->find($id);
            $cant = 0;
            $modificados = '<br/>';
            $noModificados = '<br/>';

            $em->getConnection()->beginTransaction();
            
            foreach ($actualizaciones as $exencion) {

                if ($exencion->getActualiza()) {

                    $certificado = new CertificadoExencion();

                    $certificado->setTipoRegimen($exencion->getTipoRegimen());
                    $certificado->setPorcentajeExencion($exencion->getPorcentajeExencion());
                    $certificado->setFechaDesde($exencion->getFechaDesde());
                    $certificado->setFechaHasta($exencion->getFechaHasta());
                    $certificado->setNumeroCertificado($exencion->getNumeroCertificado());
                    $em->persist($certificado);

                    $cant = $cant + 1;
                    $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->findOneByClienteProveedor($exencion->getClienteProveedor()->getId());
                    $clienteProveedor = $exencion->getClienteProveedor();

                    switch ($padron->getTipoImpuesto()) {
                        
                        case ConstanteTipoImpuesto::IVA . '2226': 
                        case ConstanteTipoImpuesto::IVA . '18':

                            if ($proveedor->getCertificadoExencionIVA() == null) {
                                $modificados .= $cant.') nuevo certificado de exencion: '.$exencion->getCuit().' '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            } else {
                                $modificados .= $cant.') '.$exencion->getCuit().' '.$proveedor->getCertificadoExencionIVA()->getTipoRegimen().' '.$proveedor->getCertificadoExencionIVA()->getPorcentajeExencion().'% - Desde: '.$proveedor->getCertificadoExencionIVA()->getFechaDesde()->format("d/m/Y").' Hasta: '.$proveedor->getCertificadoExencionIVA()->getFechaHasta()->format("d/m/Y").' => '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            }

                            $proveedor->setCertificadoExencionIVA($certificado);
                            $clienteProveedor->setExentoIVA(true);
                            break;
                        case ConstanteTipoImpuesto::IIBB:

                            if ($proveedor->getCertificadoExencionIngresosBrutos() == null) {
                                $modificados .= $cant.') nuevo certificado de exencion: '.$exencion->getCuit().' '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            } else {
                                $modificados .= $cant.') '.$exencion->getCuit().' '.$proveedor->getCertificadoExencionIngresosBrutos()->getTipoRegimen().' '.$proveedor->getCertificadoExencionIngresosBrutos()->getPorcentajeExencion().'% - Desde: '.$proveedor->getCertificadoExencionIngresosBrutos()->getFechaDesde()->format("d/m/Y").' Hasta: '.$proveedor->getCertificadoExencionIngresosBrutos()->getFechaHasta()->format("d/m/Y").' => '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            }

                            $proveedor->setCertificadoExencionIngresosBrutos($certificado);
                            $clienteProveedor->setExentoIngresosBrutos(true);
                            break;
                        case ConstanteTipoImpuesto::SUSS:

                            if ($proveedor->getCertificadoExencionSUSS() == null) {
                                $modificados .= $cant.') nuevo certificado de exencion: '.$exencion->getCuit().' '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            } else {
                                $modificados .= $cant.') '.$exencion->getCuit().' '.$proveedor->getCertificadoExencionSUSS()->getTipoRegimen().' '.$proveedor->getCertificadoExencionSUSS()->getPorcentajeExencion().'% - Desde: '.$proveedor->getCertificadoExencionSUSS()->getFechaDesde()->format("d/m/Y").' Hasta: '.$proveedor->getCertificadoExencionSUSS()->getFechaHasta()->format("d/m/Y").' => '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            }

                            $proveedor->setCertificadoExencionSUSS($certificado);
                            $clienteProveedor->setExentoSUSS(true);
                            break;
                        case ConstanteTipoImpuesto::Ganancias:

                            if ($proveedor->getCertificadoExencionGanancias() == null) {
                                $modificados .= $cant.') nuevo certificado de exencion: '.$exencion->getCuit().' '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            } else {
                                $modificados .= $cant.') '.$exencion->getCuit().' '.$proveedor->getCertificadoExencionGanancias()->getTipoRegimen().' '.$proveedor->getCertificadoExencionGanancias()->getPorcentajeExencion().'% - Desde: '.$proveedor->getCertificadoExencionGanancias()->getFechaDesde()->format("d/m/Y").' Hasta: '.$proveedor->getCertificadoExencionGanancias()->getFechaHasta()->format("d/m/Y").' => '.$exencion->getTipoRegimen().' '.$exencion->getPorcentajeExencion().'% - Desde: '.$exencion->getFechaDesde()->format("d/m/Y").' Hasta: '.$exencion->getFechaHasta()->format("d/m/Y")."<br />";
                            }

                            $proveedor->setCertificadoExencionGanancias($certificado);
                            $clienteProveedor->setExentoGanancias(true);
                            break;
                    }

                    $em->persist($proveedor);
                    $em->persist($clienteProveedor);
                    $padron->setIdEstadoPadron(ConstanteEstadoPadron::ESTADO_PADRON_CERRADO);
                    $em->persist($padron);
                }
            }

            $em->flush();
            $em->getConnection()->commit();

            if ($cant > 0) {
                $this->get('session')->getFlashBag()
                        ->add('success', "La actualizaci&oacute;n se realiz&oacute; con &eacute;xito. Se actualizaron $cant registros".$modificados);
            }

        } catch (\Exception $e) {
            
            $em->getConnection()->rollback();
            
            $this->get('session')->getFlashBag()
                        ->add('error', "Hubo un error al guardar los padrones");
        }
        
        return $this->redirect($this->generateUrl('padrones'));
        
    }

    /**
     * @Route("/confirmarPadron/", name="confirmar_padron")
     *
     * @Template("ADIFComprasBundle:Proveedor:index.padrones.html.twig")
     */
    public function confirmarPadronAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Padrones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Padrones de excencion de impuestos',
            'page_info' => 'Padrones de excencion de impuestos',
            'padronPendiente' => null
        );
    }
}
