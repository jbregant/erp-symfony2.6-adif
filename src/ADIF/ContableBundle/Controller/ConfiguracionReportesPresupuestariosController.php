<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConceptoPresupuestarioRemuneracion;
use ADIF\ContableBundle\Form\ConceptoPresupuestarioRemuneracionType;
use ADIF\ContableBundle\Form\ConceptoPresupuestarioServiciosNoPersonalesType;
use ADIF\ContableBundle\Form\ConceptoPresupuestarioNivelVentasType;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Form\ConceptoPresupuestarioDisponibilidadesType;

/**
 * Configuracion de reportes presupuestarios controller.
 *
 * @Route("/configuracionreportespresupuestarios")
 */
class ConfiguracionReportesPresupuestariosController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * 
     * @Route("/epe2", name="configuracionreportespresupuestarios_epe2")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:configuracion.epe_2.html.twig")
     */
    public function configuracionEPE2Action() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conceptosPresupuestarioDisponibilidades = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDisponibilidades')->findAll();
        $epe2Configuracion = array();
        foreach ($conceptosPresupuestarioDisponibilidades as $conceptoPresupuestarioDisponibilidades) {
            $epe2Configuracion[] = [
                'id' => $conceptoPresupuestarioDisponibilidades->getId(),
                'denominacion' => $conceptoPresupuestarioDisponibilidades->getDenominacion(),
                'cuentas' => implode(' <br> ', $conceptoPresupuestarioDisponibilidades->getCuentasContables()->toArray())
            ];
        }

        $bread = $this->base_breadcrumbs;
        $bread['Presupuesto'] = $this->generateUrl('presupuesto');
        $bread['Configuraci&oacute;n EPE 2'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n EPE 2',
            'page_info' => 'Configuraci&oacute;n EPE 2',
            'epe2Configuracion' => $epe2Configuracion
        );
    }

    /**
     * Devuelve el template para configurar epe2
     *
     * @Route("/form_epe2", name="configuracionreportespresupuestarios_form_epe2")
     * @Method("POST")   
     * @Template("ADIFContableBundle:Presupuesto:form_epe2.html.twig")
     */
    public function getFormEPE2Action(Request $request) {

        $idConcepto = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioDisponibilidades \ADIF\ContableBundle\Entity\ConceptoPresupuestarioDisponibilidades */
        $conceptosPresupuestarioDisponibilidades = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDisponibilidades')
                ->find($idConcepto);

        if (!$conceptosPresupuestarioDisponibilidades) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioDisponibilidades.');
        }

        $form = $this->createForm(new ConceptoPresupuestarioDisponibilidadesType($this->getDoctrine()->getManager($this->getEntityManager())), $conceptosPresupuestarioDisponibilidades, array(
            'action' => $this->generateUrl('configuracionreportespresupuestarios_epe2_update', array('id' => $conceptosPresupuestarioDisponibilidades->getId())),
            'method' => 'POST'
        ));

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Update de configuracion de epe2
     *
     * @Route("/form_epe2/{id}", name="configuracionreportespresupuestarios_epe2_update")
     * @Method("GET|POST")
     * @Template()  
     */
    public function updateEPE2Action($id, Request $request) {
        $requestForm = $request->request->get('adif_contablebundle_conceptopresupuestariodisponibilidades');
        $idCuentas = $requestForm['cuentasContables'];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioDisponibilidades \ADIF\ContableBundle\Entity\ConceptoPresupuestarioDisponibilidades */
        $conceptoPresupuestarioDisponibilidades = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDisponibilidades')
                ->find($id);

        if (!$conceptoPresupuestarioDisponibilidades) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioDisponibilidades.');
        }

        //borro las anteriores
        foreach ($conceptoPresupuestarioDisponibilidades->getCuentasContables() as $cuenta) {
            $conceptoPresupuestarioDisponibilidades->removeCuentasContable($cuenta);
        }

        //agrego las nuevas
        if ($idCuentas != null) {
            foreach ($idCuentas as $idCuenta) {
                $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                        ->find($idCuenta);
                $conceptoPresupuestarioDisponibilidades->addCuentasContable($cuentaContable);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('configuracionreportespresupuestarios_epe2'));
    }

    /**
     * 
     * @Route("/epe3", name="configuracionreportespresupuestarios_epe3")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:configuracion.epe_3.html.twig")
     */
    public function configuracionEPE3Action() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $tiposConceptoPresupuestarioRemuneracion = $em->getRepository('ADIFContableBundle:TipoConceptoPresupuestarioRemuneracion')->findAll();
        $epe3Configuracion = array();
        foreach ($tiposConceptoPresupuestarioRemuneracion as $tipoConceptoPresupuestarioRemuneracion) {
            $epe3Configuracion[$tipoConceptoPresupuestarioRemuneracion->getId()]['denominacion'] = $tipoConceptoPresupuestarioRemuneracion->getDescripcion();
            $epe3Configuracion[$tipoConceptoPresupuestarioRemuneracion->getId()]['conceptos'] = array();
            $conceptosPresupuestarioRemuneracion = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioRemuneracion')->findByTipoConceptoPresupuestarioRemuneracion($tipoConceptoPresupuestarioRemuneracion);
            foreach ($conceptosPresupuestarioRemuneracion as $conceptoPresupuestarioRemuneracion) {
                $epe3Configuracion[$tipoConceptoPresupuestarioRemuneracion->getId()]['conceptos'][] = [
                    'id' => $conceptoPresupuestarioRemuneracion->getId(),
                    'denominacion' => $conceptoPresupuestarioRemuneracion->getDenominacionConceptoPresupuestarioRemuneracion(),
                    'cuentas' => implode(' <br> ', $conceptoPresupuestarioRemuneracion->getCuentasContables()->toArray())
                ];
            }
        }

        $bread = $this->base_breadcrumbs;
        $bread['Presupuesto'] = $this->generateUrl('presupuesto');
        $bread['Configuraci&oacute;n EPE 3'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n EPE 3',
            'page_info' => 'Configuraci&oacute;n EPE 3',
            'epe3Configuracion' => $epe3Configuracion
        );
    }

    /**
     * Devuelve el template para configurar epe3
     *
     * @Route("/form_epe3", name="configuracionreportespresupuestarios_form_epe3")
     * @Method("POST")   
     * @Template("ADIFContableBundle:Presupuesto:form_epe3.html.twig")
     */
    public function getFormEPE3Action(Request $request) {

        $idConcepto = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioRemuneracion \ADIF\ContableBundle\Entity\ConceptoPresupuestarioRemuneracion */
        $conceptoPresupuestarioRemuneracion = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioRemuneracion')
                ->find($idConcepto);

        if (!$conceptoPresupuestarioRemuneracion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioRemuneracion.');
        }

        $form = $this->createForm(new ConceptoPresupuestarioRemuneracionType($this->getDoctrine()->getManager($this->getEntityManager())), $conceptoPresupuestarioRemuneracion, array(
            'action' => $this->generateUrl('configuracionreportespresupuestarios_epe3_update', array('id' => $conceptoPresupuestarioRemuneracion->getId())),
            'method' => 'POST',
        ));

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Update de configuracion de epe3
     *
     * @Route("/form_epe3/{id}", name="configuracionreportespresupuestarios_epe3_update")
     * @Method("GET|POST")
     * @Template()  
     */
    public function updateEPE3Action($id, Request $request) {
        $requestForm = $request->request->get('adif_contablebundle_conceptopresupuestarioremuneracion');
        $idCuentas = $requestForm['cuentasContables'];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioRemuneracion \ADIF\ContableBundle\Entity\ConceptoPresupuestarioRemuneracion */
        $conceptoPresupuestarioRemuneracion = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioRemuneracion')
                ->find($id);

        if (!$conceptoPresupuestarioRemuneracion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioRemuneracion.');
        }

        //borro las anteriores
        foreach ($conceptoPresupuestarioRemuneracion->getCuentasContables() as $cuenta) {
            $conceptoPresupuestarioRemuneracion->removeCuentasContable($cuenta);
        }

        //agrego las nuevas
        if ($idCuentas != null) {
            foreach ($idCuentas as $idCuenta) {
                $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                        ->find($idCuenta);
                $conceptoPresupuestarioRemuneracion->addCuentasContable($cuentaContable);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('configuracionreportespresupuestarios_epe3'));
    }

    /**
     * 
     * @Route("/epe5", name="configuracionreportespresupuestarios_epe5")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:configuracion.epe_5.html.twig")
     */
    public function configuracionEPE5Action() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conceptosPresupuestarioServiciosNoPersonales = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioServiciosNoPersonales')->findAll();
        $epe5Configuracion = array();
        foreach ($conceptosPresupuestarioServiciosNoPersonales as $conceptoPresupuestarioServiciosNoPersonales) {
            $epe5Configuracion[] = [
                'id' => $conceptoPresupuestarioServiciosNoPersonales->getId(),
                'denominacion' => $conceptoPresupuestarioServiciosNoPersonales->getDenominacionConceptoPresupuestarioServiciosNoPersonales(),
                'cuentas' => implode(' <br> ', $conceptoPresupuestarioServiciosNoPersonales->getCuentasContables()->toArray())
            ];
        }

        $bread = $this->base_breadcrumbs;
        $bread['Presupuesto'] = $this->generateUrl('presupuesto');
        $bread['Configuraci&oacute;n EPE 5'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n EPE 5',
            'page_info' => 'Configuraci&oacute;n EPE 5',
            'epe5Configuracion' => $epe5Configuracion
        );
    }

    /**
     * Devuelve el template para configurar epe5
     *
     * @Route("/form_epe5", name="configuracionreportespresupuestarios_form_epe5")
     * @Method("POST")   
     * @Template("ADIFContableBundle:Presupuesto:form_epe5.html.twig")
     */
    public function getFormEPE5Action(Request $request) {

        $idConcepto = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioServiciosNoPersonales \ADIF\ContableBundle\Entity\ConceptoPresupuestarioServiciosNoPersonales */
        $conceptoPresupuestarioServiciosNoPersonales = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioServiciosNoPersonales')
                ->find($idConcepto);

        if (!$conceptoPresupuestarioServiciosNoPersonales) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioServiciosNoPersonales.');
        }

        $form = $this->createForm(new ConceptoPresupuestarioServiciosNoPersonalesType($this->getDoctrine()->getManager($this->getEntityManager())), $conceptoPresupuestarioServiciosNoPersonales, array(
            'action' => $this->generateUrl('configuracionreportespresupuestarios_epe5_update', array('id' => $conceptoPresupuestarioServiciosNoPersonales->getId())),
            'method' => 'POST',
        ));

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Update de configuracion de epe5
     *
     * @Route("/form_epe5/{id}", name="configuracionreportespresupuestarios_epe5_update")
     * @Method("GET|POST")
     * @Template()  
     */
    public function updateEPE5Action($id, Request $request) {
        $requestForm = $request->request->get('adif_contablebundle_conceptopresupuestarioserviciosnopersonales');
        $idCuentas = $requestForm['cuentasContables'];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioServiciosNoPersonales \ADIF\ContableBundle\Entity\ConceptoPresupuestarioServiciosNoPersonales */
        $conceptoPresupuestarioServiciosNoPersonales = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioServiciosNoPersonales')
                ->find($id);

        if (!$conceptoPresupuestarioServiciosNoPersonales) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioServiciosNoPersonales.');
        }

        //borro las anteriores
        foreach ($conceptoPresupuestarioServiciosNoPersonales->getCuentasContables() as $cuenta) {
            $conceptoPresupuestarioServiciosNoPersonales->removeCuentasContable($cuenta);
        }

        //agrego las nuevas
        if ($idCuentas != null) {
            foreach ($idCuentas as $idCuenta) {
                $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                        ->find($idCuenta);
                $conceptoPresupuestarioServiciosNoPersonales->addCuentasContable($cuentaContable);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('configuracionreportespresupuestarios_epe5'));
    }

    /**
     * 
     * @Route("/epe7", name="configuracionreportespresupuestarios_epe7")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:configuracion.epe_7.html.twig")
     */
    public function configuracionEPE7Action() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conceptosPresupuestarioNivelVentas = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioNivelVentas')->findAll();
        $epe7Configuracion = array();
        foreach ($conceptosPresupuestarioNivelVentas as $conceptoPresupuestarioNivelVentas) {
            $epe7Configuracion[] = [
                'id' => $conceptoPresupuestarioNivelVentas->getId(),
                'denominacion' => $conceptoPresupuestarioNivelVentas->getDenominacion(),
                'cuentas' => implode(' <br> ', $conceptoPresupuestarioNivelVentas->getCuentasContables()->toArray())
            ];
        }

        $bread = $this->base_breadcrumbs;
        $bread['Presupuesto'] = $this->generateUrl('presupuesto');
        $bread['Configuraci&oacute;n EPE 7'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n EPE 7',
            'page_info' => 'Configuraci&oacute;n EPE 7',
            'epe7Configuracion' => $epe7Configuracion
        );
    }

    /**
     * Devuelve el template para configurar epe7
     *
     * @Route("/form_epe7", name="configuracionreportespresupuestarios_form_epe7")
     * @Method("POST")   
     * @Template("ADIFContableBundle:Presupuesto:form_epe7.html.twig")
     */
    public function getFormEPE7Action(Request $request) {

        $idConcepto = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptosPresupuestarioNivelVentas \ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas */
        $conceptosPresupuestarioNivelVentas = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioNivelVentas')
                ->find($idConcepto);

        if (!$conceptosPresupuestarioNivelVentas) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioNivelVentas.');
        }

        $form = $this->createForm(new ConceptoPresupuestarioNivelVentasType($this->getDoctrine()->getManager($this->getEntityManager())), $conceptosPresupuestarioNivelVentas, array(
            'action' => $this->generateUrl('configuracionreportespresupuestarios_epe7_update', array('id' => $conceptosPresupuestarioNivelVentas->getId())),
            'method' => 'POST',
        ));

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Update de configuracion de epe7
     *
     * @Route("/form_epe7/{id}", name="configuracionreportespresupuestarios_epe7_update")
     * @Method("GET|POST")
     * @Template()  
     */
    public function updateEPE7Action($id, Request $request) {
        $requestForm = $request->request->get('adif_contablebundle_conceptopresupuestarionivelventas');
        $idCuentas = $requestForm['cuentasContables'];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptosPresupuestarioNivelVentas \ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas */
        $conceptosPresupuestarioNivelVentas = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioNivelVentas')
                ->find($id);

        if (!$conceptosPresupuestarioNivelVentas) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioNivelVentas.');
        }

        //borro las anteriores
        foreach ($conceptosPresupuestarioNivelVentas->getCuentasContables() as $cuenta) {
            $conceptosPresupuestarioNivelVentas->removeCuentasContable($cuenta);
        }

        //agrego las nuevas
        if ($idCuentas != null) {
            foreach ($idCuentas as $idCuenta) {
                $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                        ->find($idCuenta);
                $conceptosPresupuestarioNivelVentas->addCuentasContable($cuentaContable);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('configuracionreportespresupuestarios_epe7'));
    }

    /**
     * 
     * @Route("/epe8", name="configuracionreportespresupuestarios_epe8")
     * @Method("GET")
     * @Template("ADIFContableBundle:Presupuesto:configuracion.epe_8.html.twig")
     */
    public function configuracionEPE8Action() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $conceptosPresupuestarioDotacionPersonal = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDotacionPersonal')->findAll();
        $epe8Configuracion = array();
        foreach ($conceptosPresupuestarioDotacionPersonal as $conceptoPresupuestarioDotacionPersonal) {
            $categoriasRepositorio = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Categoria')->getCategoriasByIds(explode(',', $conceptoPresupuestarioDotacionPersonal->getCategorias()));
            $categorias = array();
            foreach ($categoriasRepositorio as $value) {
                $categorias[] = $value->getNombre() . ' - ' . $value->getIdConvenio();
            }
            $epe8Configuracion[] = [
                'id' => $conceptoPresupuestarioDotacionPersonal->getId(),
                'denominacion' => $conceptoPresupuestarioDotacionPersonal->getDenominacion(),
                'categorias' => implode(' <br> ', $categorias)
            ];
        }

        $bread = $this->base_breadcrumbs;
        $bread['Presupuesto'] = $this->generateUrl('presupuesto');
        $bread['Configuraci&oacute;n EPE 8'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuraci&oacute;n EPE 8',
            'page_info' => 'Configuraci&oacute;n EPE 8',
            'epe8Configuracion' => $epe8Configuracion
        );
    }

    /**
     * Devuelve el template para configurar epe8
     *
     * @Route("/form_epe8", name="configuracionreportespresupuestarios_form_epe8")
     * @Method("POST")   
     * @Template("ADIFContableBundle:Presupuesto:form_epe8.html.twig")
     */
    public function getFormEPE8Action(Request $request) {

        $idConcepto = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $categorias = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Categoria')->findAll();

        /* @var $conceptoPresupuestarioDotacionPersonal \ADIF\ContableBundle\Entity\ConceptoPresupuestarioDotacionPersonal */
        $conceptoPresupuestarioDotacionPersonal = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDotacionPersonal')
                ->find($idConcepto);

        if (!$conceptoPresupuestarioDotacionPersonal) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioDotacionPersonal.');
        }

        $idsCategoriasConcepto = explode(',', $conceptoPresupuestarioDotacionPersonal->getCategorias());

        $idsCategorias = array();

        foreach ($idsCategoriasConcepto as $value) {
            $idsCategorias[$value] = $value;
        }

        return array(
            'categorias' => $categorias,
            'idsCategorias' => $idsCategorias,
            'concepto' => $conceptoPresupuestarioDotacionPersonal
        );
    }

    /**
     * Update de configuracion de epe8
     *
     * @Route("/form_epe8/{id}", name="configuracionreportespresupuestarios_epe8_update")
     * @Method("GET|POST")
     * @Template()  
     */
    public function updateEPE8Action($id, Request $request) {
        $requestForm = $request->request->get('form_epe8');
        $categorias = $requestForm['categorias'] != null ? implode(',', $requestForm['categorias']) : '';

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $conceptoPresupuestarioDotacionPersonal \ADIF\ContableBundle\Entity\ConceptoPresupuestarioDotacionPersonal */
        $conceptoPresupuestarioDotacionPersonal = $em->getRepository('ADIFContableBundle:ConceptoPresupuestarioDotacionPersonal')
                ->find($id);

        if (!$conceptoPresupuestarioDotacionPersonal) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPresupuestarioDotacionPersonal.');
        }

        $conceptoPresupuestarioDotacionPersonal->setCategorias($categorias);
        $em->flush();

        return $this->redirect($this->generateUrl('configuracionreportespresupuestarios_epe8'));
    }

}
