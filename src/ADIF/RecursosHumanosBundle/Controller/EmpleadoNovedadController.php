<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad;
use ADIF\RecursosHumanosBundle\Entity\Persona;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Abstract;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * EmpleadoNovedad controller.
 *
 * @Route("/empleado_novedades")
 * @Security("has_role('ROLE_RRHH_ALTA_NOVEDADES')")
 */
class EmpleadoNovedadController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Lists all EmpleadoNovedad entities.
     *
     * @Route("/importar", name="empleado_novedades_importar")
     * @Method("GET")
     * @Template()
     */
    public function indexImportarAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $bread = $this->base_breadcrumbs;
        $bread['EmpleadoNovedad'] = null;

        $form = $this->createUploadForm()->createView();
        
        $this->get('session')->remove('empleado_novedad_importar_data');

        return array(
            'form' => $form,
            'breadcrumbs' => $bread,
            'page_title' => 'Importar novedades'
        );
    }

    private function createUploadForm() {
        return
            $this->createFormBuilder(null, array(
                'action' => $this->generateUrl('empleado_novedades_upload'),
                'method' => 'POST',
            ))
            ->add('novedades', 'entity', array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\Concepto',
                'required' => true,
                'label' => 'Novedad',
                'attr' => array('class' => ' form-control choice  '),
                'empty_value' => '-- Elija una Novedad --',
                'property' => 'nombreCodigo',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.esNovedad = 1')
                        ->orderBy('c.descripcion', 'ASC');
            }))
            ->add('fechaNovedad', 'date', array(
                'required' => true,
                'label' => 'Fecha de la novedad',
                'attr' => array('class' => ' form-control  datepicker '),
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'))
            ->add('file', 'file', array(
                'required' => true,
                'label' => 'Archivo',
                'mapped' => false
            ))
            ->add('submit', 'submit', array('label' => 'Previsualizar'))
            ->getForm();
    }

    /**
     * Lists all EmpleadoNovedad entities.
     *
     * @Route("/upload", name="empleado_novedades_upload")
     * @Method("POST|GET")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoNovedad:indexImportar.html.twig")
     */
    public function uploadAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $form = $this->createUploadForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $uploadDir = dirname($this->container->getParameter('kernel.root_dir')) . '/web/bundles/adifrecursoshumanos/importaciones';
            $newFile = 'importacion_novedad_' . $form['novedades']->getData()->getId() . '.xls';
            $form['file']->getData()->move($uploadDir, $newFile);
            
            $conceptoNovedad =  $form['novedades']->getData();
            $codigoNovedad = $conceptoNovedad->getCodigo();
            $fechaNovedad = $form['fechaNovedad']->getData();
                       
            $objReader = PHPExcel_IOFactory::createReaderForFile($uploadDir . '/' . $newFile);
            if($objReader->canRead($uploadDir . '/' . $newFile)){
                $objReader->setReadDataOnly(true);
                try {
                    $objPHPExcel = $objReader->load($uploadDir . '/' . $newFile);
                } catch (Exception $e) {
                    echo $e->printStackTrace();
                    die;
                }
                $sheet = $objPHPExcel->getActiveSheet();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $empleadosSinNovedad = array();
                $cuilEmpleadosInexistentes = array();
                $empleadosDesactivados = array();
                $empleadosYaCargados = array();
                $empleadosConvenioErroneo = array();
                $empleadosNovedades = array();

                //  Loop through each row of the worksheet in turn
                for ($row = 1; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                    $cuil = $rowData[0][0];
                    $valor = $rowData[0][1];
                    $dias = isset($rowData[0][2]) ? $rowData[0][2] : null;
                    $numero_liq = isset($rowData[0][3]) ? $rowData[0][3] : null;

                    if (!$cuil && !$valor) {
                        //$cuilEmpleadosInexistentes[] = $cuil;
                        continue;
                    }

                    /* @var $empleado Empleado */
                    $empleadoResult = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                        ->createQueryBuilder('e')
                        ->select('e', 'p')
                            ->innerJoin('e.persona', 'p')
                        ->where('p.cuil = :cuil')
                            ->setParameter('cuil', $cuil)
                        ->getQuery()
                        ->setMaxResults(1)
                        ->getResult();

                    //Cheque si existe el empleado con ese CUIL
                    if (!$empleadoResult) {
                        $cuilEmpleadosInexistentes[] = $cuil;
                        continue;
                    }

                    $empleado = $empleadoResult[0];

                    // Si no viene el valor en el Excel
                    if (!$valor || !is_numeric($valor)) {
                        $empleadosSinNovedad[] = $empleado;
                        continue;
                    }
                    
                    // Si el empleado está desactivado
                    if ($empleado->getActivo() == 0) {
                        $empleadosDesactivados[] = $empleado;
                        continue;
                    }

                    /* @var $conceptoNovedad \ADIF\RecursosHumanosBundle\Entity\Concepto */

                    if(!($conceptoNovedad->getConvenios()->contains($empleado->getConvenio()))){
                        // El convenio del empleado no conicide con el de la novedad
                        $empleadosConvenioErroneo[] = $empleado;
                        continue;
                    }

                    $novedadesActuales = $empleado->getNovedades();
                    $yaAsociada = false;
                    foreach ($novedadesActuales as $empleadoNovedad) {
                        if ($empleadoNovedad->getFechaBaja() == null && $empleadoNovedad->getConcepto()->getCodigo() == $codigoNovedad) {
                            // Si el empleado ya tiene la novedad cargada
                            $empleadosYaCargados[] = $empleadoNovedad;
                            $yaAsociada = true;
                            break;
                        }
                    }

                    if (!$yaAsociada){
                        $en = new EmpleadoNovedad($this->getDoctrine()->getManager($this->getEntityManager()));
                        $en->setEmpleado($empleado);
                        $en->setConcepto($conceptoNovedad);
                        $en->setFechaAlta($fechaNovedad);
                        $en->setValor($valor);
                        if($dias && $numero_liq){
                            $en->setDias($dias);
                            $en->setLiquidacionAjuste($em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->findOneByNumero($numero_liq));
                        }
                        $empleadosNovedades[] = $en;
                    }                
                }
            } else {
                echo "Formato de archivo inv&aacute;lido";
                die();  
            }
            
            $this->get('session')->set('empleado_novedad_importar_data', 
                array(
                    'fecha' => $fechaNovedad,
                    'novedad' => $conceptoNovedad,
                    'empleados_sin_novedad' => $empleadosSinNovedad,
                    'cuil_empleados_inexistentes' => $cuilEmpleadosInexistentes,
                    'empleados_ya_cargados' => $empleadosYaCargados,
                    'empleados_convenio_erroneo' => $empleadosConvenioErroneo,
                    'empleados_desactivados' => $empleadosDesactivados,
                    'empleados_novedades' => $empleadosNovedades,
                )
            );
            
        } else {
            echo $form->getErrorsAsString();
            die();
        }

        return $this->redirect($this->generateUrl('empleado_novedades_importar_previsualizar'));
    }
    
    /**
     * Lists all EmpleadoNovedad entities.
     *
     * @Route("/importar/previsualizar", name="empleado_novedades_importar_previsualizar")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoNovedad:previsualizar.html.twig")
     */
    public function previsualizarImportarAction(){
        
        $data = $this->get('session')->get('empleado_novedad_importar_data');
        
        $bread = $this->base_breadcrumbs;
        $bread['Novedades'] = null;

        return array(
            'novedad' => $data['novedad'],
            'fecha' => $data['fecha'],
            'empleados_sin_novedad' => $data['empleados_sin_novedad'],
            'cuil_empleados_inexistentes' => $data['cuil_empleados_inexistentes'],
            'empleados_ya_cargados' => $data['empleados_ya_cargados'],
            'empleados_convenio_erroneo' =>  $data['empleados_convenio_erroneo'],
            'empleados_novedades' => $data['empleados_novedades'],
            'empleados_desactivados' => $data['empleados_desactivados'],
            'breadcrumbs' => $bread,
            'page_title' => 'Previsualizar importación'
        );
    }
    
    /**
     * Persistir la novedad
     *
     * @Route("/importar/guardar", name="empleado_novedades_importar_guardar")
     * @Method("GET")
     */
    public function guardarImportarAction(){
        $data = $this->get('session')->get('empleado_novedad_importar_data');        
        if($data){
            $em = $this->getDoctrine()->getManager($this->getEntityManager());            
            foreach($data['empleados_novedades'] as $empleadoNovedad){
                /* @var $empleadoNovedad EmpleadoNovedad */
                $en_mergeada = $em->merge($empleadoNovedad);                
                $em->persist($en_mergeada);
            }
            $em->flush();
            $this->get('session')->remove('empleado_novedad_importar_data');
            return $this->redirect($this->generateUrl('empleados'));
        } else {
            throw $this->createNotFoundException('No se puede importar la Novedad.');
        }        
    }

    /**
     * Devuelve las novedades historicas para un empleado en particular
     *
     * @Route("/historico/{idEmpleado}", name="empleado_novedades_historicas")
     * @Method("POST|GET")
     * @Template()
     */
    public function novedadesHistoricasAction($idEmpleado = null) {
        /* @var $em EntityManagerInterface */

        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleadoConcepto', $this->getEntityManager());
        $novedadesHistoricas = $repository
                ->createQueryBuilder('lec')
                ->select(
                    'partial cv.{id, codigo, descripcion, formula}', 
                    'partial lec.{id, monto}', 
                    'partial le.{id}', 
                    'partial l.{id, fechaCierreNovedades, fechaAlta, numero}',
                    'partial en.{id, valor}')
                ->innerJoin('lec.liquidacionEmpleado', 'le')
                ->innerJoin('le.liquidacion', 'l')
                ->innerJoin('lec.conceptoVersion', 'cv')
                ->innerJoin('lec.empleadoNovedad', 'en')
                ->where('le.empleado = :idEmpleado')
                ->setParameter('idEmpleado', $idEmpleado)
                ->orderBy('l.fechaCierreNovedades', 'DESC')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse($novedadesHistoricas);
    }

}
