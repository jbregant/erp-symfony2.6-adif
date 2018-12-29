<?php

namespace ADIF\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\UserBundle\Controller\SecurityController as FOSController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use ADIF\AutenticacionBundle\Entity\Usuario;
use Doctrine\ORM\EntityRepository;

/**
* Security Controller.
*
* @Route("/api")
 */
class SecurityController extends FOSController
{
    /**
    * @Route("/login", name="user_login")
    * @Method("POST")
    * @param Request $request
    * @return Response
    */
    public function loginAction(Request $request){

        $request = $this->getRequest();
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $deviceId = $request->request->get('deviceId');
        $userRol  = $request->request->get('userRol');

        $user_manager = $this->get('fos_user.user_manager');
        $em_user = $this->getDoctrine()->getManager('siga_autenticacion');
        $factory = $this->get('security.encoder_factory');

        $user = $user_manager->findUserByUsername($username);

        if(!$user){
          $myresponse = array(
              "content" => array('error' => "No Existe el Usuario")
          );

          $finalResponse = json_encode($myresponse);
          $response = new Response($finalResponse);
          $response->headers->set('Content-Type', 'application/json');
          return $response;
        }

        $roleLen = strlen($userRol);

        if($roleLen<9){
          $myresponse = array(
              "content" => array('error' => "Rol no Autorizado")
          );

          $finalResponse = json_encode($myresponse);
          $response = new Response($finalResponse);
          $response->headers->set('Content-Type', 'application/json');
          return $response;
        }

        $encoder = $factory->getEncoder($user);
        $salt = $user->getSalt();

        $bool = ($encoder->isPasswordValid($user->getPassword(), $password, $salt)) ? true : false;

        if($bool){
            $nowTime = new \DateTime;
            $loginTime = $nowTime->format('Y-m-d H:i:s');

            $sql = "SELECT id as `id`, username as `UserName`, ? as `DeviceId`, email as `email`, ? as `SyncTime`
                      FROM usuario
                     WHERE salt= ?";

            $stmt = $em_user->getConnection()->prepare($sql);
            $stmt->bindValue(1, $deviceId);
            $stmt->bindValue(2, $loginTime);
            $stmt->bindValue(3, $salt);
            $stmt->execute();

            $sql = "SELECT us.id as `id`, us.username as `UserName`, ? as `DeviceId`, email as `email`, ? as `SyncTime`,
                    case
                        when g.roles like '%ROL_API_AL%' then 'ROL_API_AL'
                        when g.roles like '%ROL_API_MR%' then 'ROL_API_MR'
                        when g.roles like '%ROL_API_MN_PO%' then 'ROL_API_MN_PO'
                    end as `UserRol`
                    FROM usuario as us, usuario_grupo_empresa as uge, grupo as g
                    WHERE us.salt= ?
                    AND uge.id_usuario = us.id
                    AND uge.id_grupo = g.id
                    AND g.roles like '%API%'";

            $stmt = $em_user->getConnection()->prepare($sql);
            $stmt->bindValue(1, $deviceId);
            $stmt->bindValue(2, $loginTime);
            $stmt->bindValue(3, $salt);
            $stmt->execute();

            $newToken = md5(uniqid(rand(), true));

            $myresponse = array(
                "content" => array (
                    "Usuario" => $stmt->fetchAll(),
                    "Authtoken" => $newToken
                  )
            );

            $userID = $user->getId();

            $sql = "DELETE FROM AccessToken WHERE user_id= ?";
            $stmt = $em_user->getConnection()->prepare($sql);
            $stmt->bindValue(1, $userID);
            $stmt->execute();

            $sql_us = "INSERT INTO AccessToken SET user_id = ?, token = ?, expires_at = ?";
            $stus = $em_user->getConnection()->prepare($sql_us);
            $stus->bindValue(1, $userID);
            $stus->bindValue(2, $newToken);
            $stus->bindValue(3, $loginTime);
            $stus->execute();

            $sql_validar = "SELECT count(*)
                            FROM usuario as us, usuario_grupo_empresa as uge, grupo as g
                            WHERE us.id= ?
                            AND uge.id_usuario = us.id
                            AND uge.id_grupo = g.id
                            AND g.roles like ? ";

            $stus = $em_user->getConnection()->prepare($sql_validar);
            $stus->bindValue(1, $userID);
            $stus->bindValue(2, '%'.$userRol.'%');
            $stus->execute();

            $sqlVal = $stus->fetchColumn();

            if($sqlVal != "0" && $userRol){
                $finalResponse = json_encode($myresponse);
                $response = new Response($finalResponse);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else{
                $myresponse = array(
                    "content" => array('error' => "Rol no Autorizado")
                );

                $finalResponse = json_encode($myresponse);
                $response = new Response($finalResponse);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        else{
          $myresponse = array(
              "content" => array('error' => "Datos Incorrectos")
          );

          $finalResponse = json_encode($myresponse);
          $response = new Response($finalResponse);
          $response->headers->set('Content-Type', 'application/json');
          return $response;
        }
  }







   /**************************************************************************************************************/
   /**************************************************************************************************************/
   /**
    * @Route("/logout", name="user_logout")
    * @Method("POST")
    * @param Request $request
    * @return Response
    */
   public function logoutDeviceAction(Request $request){

        $request = $this->getRequest();
        $userId = $request->request->get('userId');
        $em_user = $this->getDoctrine()->getManager('siga_autenticacion');

        $sql = "DELETE FROM AccessToken WHERE user_id= ?";
        $stmt = $em_user->getConnection()->prepare($sql);
        $stmt->bindValue(1, $userId);

        if($stmt->execute()){
            $myresponse = true;
        }
        else {
            $myresponse = false;
        }

        $finalResponse = json_encode($myresponse);
        $response = new Response($finalResponse);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
   }







   /**************************************************************************************************************/
   /**************************************************************************************************************/
   /**
    * @Route("/update_config", name="update_config")
    * @Method("GET")
    * @param Request $request
    * @return Response
    */
   public function getConfig(Request $request){

        $request = $this->getRequest();
        $userId = $request->query->get('userId');
        $userRol = $request->query->get('userRol');
        $deviceId = $request->query->get('deviceId');
        $tokenId = $request->query->get('tokenId');
        $tableName = $request->query->get('tableName');

        if($tableName==""){$tableName = "all";}

        $tablas = array ();
        $em_user = $this->getDoctrine()->getManager('siga_autenticacion');
        $em_inventario = $this->getDoctrine()->getManager('adif_inventario');

        /*$sql_validar = "SELECT expires_at FROM AccessToken
                          WHERE user_id= ? and token = ?";*/

        $sql_validar = "SELECT count(*)
                        FROM usuario as us, AccessToken as t, usuario_grupo_empresa as uge, grupo as g
                        WHERE us.id= ?
                        AND t.token = ?
                        AND t.user_id = us.id
                        AND uge.id_usuario = us.id
                        AND uge.id_grupo = g.id
                        AND g.roles like ? ";

        $stus = $em_user->getConnection()->prepare($sql_validar);
        $stus->bindValue(1, $userId);
        $stus->bindValue(2, $tokenId);
        $stus->bindValue(3, '%'.$userRol.'%');
        $stus->execute();

        $sqlVal = $stus->fetchColumn();
        $now = new \DateTime();

        /*$objDT = \DateTime::createFromFormat('Y-m-d H:i:s', $sqlVal);
        $timeLeft = $objDT->diff($now);

        if($timeLeft->format('%i') < 20){*/
        if($sqlVal != "0"){

            // Config - General

            if($tableName=="all" || $tableName=="estado_conservacion"){
                $sql_ec = "SELECT id, denominacion, id_empresa FROM estado_conservacion";
                $stec = $em_inventario->getConnection()->prepare($sql_ec);
                $stec->execute();
                array_push($tablas, array('nombre' => "estado_conservacion", 'rows' => array($stec->fetchAll())));
            }

            if($tableName=="all" || $tableName=="linea"){
                $sql_li = "SELECT id, denominacion, id_empresa FROM linea";
                $stli = $em_inventario->getConnection()->prepare($sql_li);
                $stli->execute();
                array_push($tablas, array('nombre' => "linea", 'rows' => array($stli->fetchAll())));
            }

            if($tableName=="all" || $tableName=="operador"){
                $sql_op = "SELECT id, denominacion, id_empresa FROM operador";
                $stop = $em_inventario->getConnection()->prepare($sql_op);
                $stop->execute();
                array_push($tablas, array('nombre' => "operador", 'rows' => array($stop->fetchAll())));
            }

            if($tableName=="all" || $tableName=="estacion"){
                $sql_es = "SELECT id, denominacion, numero, id_linea, id_ramal, id_empresa FROM estacion";
                $stes = $em_inventario->getConnection()->prepare($sql_es);
                $stes->execute();
                array_push($tablas, array('nombre' => "estacion", 'rows' => array($stes->fetchAll())));
            }

            if($tableName=="all" || $tableName=="unidad_medida"){
                $sql_um = "SELECT id, denominacion FROM unidad_medida";
                $stum = $em_inventario->getConnection()->prepare($sql_um);
                $stum->execute();
                array_push($tablas, array('nombre' => "unidad_medida", 'rows' => array($stum->fetchAll())));
            }

            if($tableName=="all" || $tableName=="fabricante"){
                $sql_fa = "SELECT id, denominacion, idEmpresa as `id_empresa` FROM fabricante";
                $stfa = $em_inventario->getConnection()->prepare($sql_fa);
                $stfa->execute();
                array_push($tablas, array('nombre' => "fabricante", 'rows' => array($stfa->fetchAll())));
            }

            // Config - Activo Lineal (ROL_API_AL)

            if($userRol=="ROL_API_AL"){

              if($tableName=="all" || $tableName=="corredor"){
                  $sql_gr = "SELECT id, denominacion FROM corredor";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "corredor", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="division"){
                  $sql_gr = "SELECT id, denominacion FROM division";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "division", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="categorizacion"){
                  $sql_gr = "SELECT id, denominacion FROM categorizacion";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "categorizacion", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="tipo_via"){
                  $sql_gr = "SELECT id, denominacion FROM tipo_via";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "tipo_via", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="ramal"){
                  $sql_gr = "SELECT id, id_linea, denominacion FROM ramal";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "ramal", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="atributo"){
                  $sql_gr = "SELECT id, denominacion FROM atributo";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "atributo", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="valores_atributo"){
                  $sql_gr = "SELECT id, id_atributo, denominacion FROM valores_atributo";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "valores_atributo", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="tipo_activo"){
                  $sql_gr = "SELECT id, denominacion FROM tipo_activo";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "tipo_activo", 'rows' => array($stgr->fetchAll())));
              }

            }

            // Config - Material Rodante (ROL_API_MR)

            if($userRol=="ROL_API_MR"){

              if($tableName=="all" || $tableName=="grupo_rodante"){
                  $sql_gr = "SELECT id, denominacion FROM grupo_rodante";
                  $stgr = $em_inventario->getConnection()->prepare($sql_gr);
                  $stgr->execute();
                  array_push($tablas, array('nombre' => "grupo_rodante", 'rows' => array($stgr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="tipo_rodante"){
                  $sql_tr = "SELECT id, denominacion, id_grupo_rodante FROM tipo_rodante";
                  $sttr = $em_inventario->getConnection()->prepare($sql_tr);
                  $sttr->execute();
                  array_push($tablas, array('nombre' => "tipo_rodante", 'rows' => array($sttr->fetchAll())));
              }

              if($tableName=="all" || $tableName=="marca"){
                  $sql_ma = "SELECT id, denominacion, id_empresa FROM marca";
                  $stma = $em_inventario->getConnection()->prepare($sql_ma);
                  $stma->execute();
                  array_push($tablas, array('nombre' => "marca", 'rows' => array($stma->fetchAll())));
              }

              if($tableName=="all" || $tableName=="servicio"){
                  $sql_se = "SELECT id, denominacion, id_empresa FROM servicio";
                  $stse = $em_inventario->getConnection()->prepare($sql_se);
                  $stse->execute();
                  array_push($tablas, array('nombre' => "servicio", 'rows' => array($stse->fetchAll())));
              }

              if($tableName=="all" || $tableName=="tipo_movimiento_rodante"){
                  $sql_se = "SELECT id, denominacion, funcion FROM tipo_movimiento_rodante";
                  $stse = $em_inventario->getConnection()->prepare($sql_se);
                  $stse->execute();
                  array_push($tablas, array('nombre' => "tipo_movimiento_rodante", 'rows' => array($stse->fetchAll())));
              }

              if($tableName=="all" || $tableName=="modelo"){
                  $sql_mo = "SELECT id, denominacion, id_empresa FROM modelo";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "modelo", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="estado_inventario"){
                  $sql_mo = "SELECT id, denominacion, id_empresa FROM estado_inventario";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "estado_inventario", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="codigo_trafico"){
                  $sql_mo = "SELECT id, denominacion, id_empresa FROM codigo_trafico";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "codigo_trafico", 'rows' => array($stmo->fetchAll())));
              }

            }

            // Config - Material Nuevo y Material Producido  (ROL_API_MN_PO)

            if($userRol=="ROL_API_MN_PO"){

              if($tableName=="all" || $tableName=="grupo_material"){
                  $sql_mo = "SELECT id, denominacion, id_empresa FROM grupo_material";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "grupo_material", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="tipo_material"){
                  $sql_mo = "SELECT id, denominacion, id_empresa FROM tipo_material";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "tipo_material", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="estado_servicio"){
                  $sql_mo = "SELECT id, denominacion FROM estado_servicio";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "estado_servicio", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="tipo_movimiento_material"){
                  $sql_mo = "SELECT id, denominacion, id_empresa FROM tipo_movimiento_material";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "tipo_movimiento_material", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="almacen"){
                  $sql_mo = "SELECT id, denominacion FROM almacen";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "almacen", 'rows' => array($stmo->fetchAll())));
              }

              if($tableName=="all" || $tableName=="origen"){
                  $sql_mo = "SELECT id, denominacion FROM origen";
                  $stmo = $em_inventario->getConnection()->prepare($sql_mo);
                  $stmo->execute();
                  array_push($tablas, array('nombre' => "origen", 'rows' => array($stmo->fetchAll())));
              }

            }


            /*$loginTime = $now->format('Y-m-d H:i:s');
            $sql_us = "UPDATE AccessToken SET expires_at = ?
                       WHERE user_id= ? and token = ?";

            $stus = $em_user->getConnection()->prepare($sql_us);
            $stus->bindValue(1, $loginTime);
            $stus->bindValue(2, $userId);
            $stus->bindValue(3, $tokenId);
            $stus->execute();*/

            $myresponse = array(
                "content" => array('tablas' => array($tablas))
            );

            $finalResponse = json_encode($myresponse);
            $response = new Response($finalResponse);
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }
        else{

            /*$request = $this->getRequest();
            $em_user = $this->getDoctrine()->getManager('siga_autenticacion');

            $sql = "DELETE FROM AccessToken
                    WHERE user_id= ?";

            $stmt = $em_user->getConnection()->prepare($sql);
            $stmt->bindValue(1, $userId);
            $stmt->execute();*/

            $myresponse = array(
                "content" => array('error' => "Unauthorized Role")
            );

            $finalResponse = json_encode($myresponse);
            $response = new Response($finalResponse);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }







    /**************************************************************************************************************/
    /**************************************************************************************************************/
    /**
    * @Route("/hoja_ruta", name="hoja_de_ruta")
    * @Method("GET")
    * @param Request $request
    * @return Response
    */
    public function getHojaRuta(Request $request)
    {

        $request = $this->getRequest();
        $userId = $request->query->get('userId');
        $userRol = $request->query->get('userRol');
        $deviceId = $request->query->get('deviceId');
        $tokenId = $request->query->get('tokenId');
        $rutaID = $request->query->get('rutaID');

        $rutas_final = array();
        $items_final = array();

        $em_user = $this->getDoctrine()->getManager('siga_autenticacion');
        $em_inventario = $this->getDoctrine()->getManager('adif_inventario');

        $sql_validar = "SELECT count(*)
                        FROM usuario as us, AccessToken as t, usuario_grupo_empresa as uge, grupo as g
                        WHERE us.id= ?
                        AND t.token = ?
                        AND t.user_id = us.id
                        AND uge.id_usuario = us.id
                        AND uge.id_grupo = g.id
                        AND g.roles like ? ";

        $stus = $em_user->getConnection()->prepare($sql_validar);
        $stus->bindValue(1, $userId);
        $stus->bindValue(2, $tokenId);
        $stus->bindValue(3, '%'.$userRol.'%');
        $stus->execute();

        $sqlVal = $stus->fetchColumn();

        if($sqlVal != "0"){

            if($userRol=="ROL_API_AL"){
                $select_denomination = '%lineal%';
            }else if($userRol=="ROL_API_MR"){
                $select_denomination = '%rodante%';
            }else if($userRol=="ROL_API_MN_PO"){
                $select_denomination = '%producido%';
            }else{
                $select_denomination = '%nuevo%';
            }

            $sql_get_id_tipo_material = "SELECT id
                                         FROM tipo_material
                                         WHERE denominacion like ? ";

            $sttm = $em_inventario->getConnection()->prepare($sql_get_id_tipo_material);
            $sttm->bindValue(1, $select_denomination);
            $sttm->execute();

            $TMId = $sttm->fetchColumn();

            $sql_get_hojas_ruta = "SELECT id, denominacion, id_tipo_material, id_usuario_asignado,
		                                      id_estado_hoja_ruta, fecha_vencimiento, es_inspeccion_tecnica,
		                                      id_tipo_relevamiento, id_levantamiento
                                   FROM hoja_ruta
                                   WHERE id_tipo_material = ?
                                   AND id_usuario_asignado = ?
                                   AND fecha_baja is null";

            $sthr = $em_inventario->getConnection()->prepare($sql_get_hojas_ruta);
            $sthr->bindValue(1, $TMId);
            $sthr->bindValue(2, $userId);
            $sthr->execute();

            $rutas = $sthr->fetchAll();

            foreach ($rutas as $ruta){
                $key='id';
                if($userRol=="ROL_API_AL"){

                    $sql_get_items_hoja_ruta = "SELECT id, id_hoja_ruta, id_linea, id_operador,
                                                       id_division, id_activo_lineal, id_tipo_activo
                                                FROM item_hoja_ruta_activo_lineal
                                                WHERE id_hoja_ruta = ?
                                                AND fecha_baja is null";

                }else if($userRol=="ROL_API_MR"){

                    $sql_get_items_hoja_ruta = "SELECT id, id_hoja_ruta, id_operador, id_estacion,
                                                       id_grupo_rodante, id_material_rodante, id_tipo_rodante
                                                FROM item_hoja_ruta_rodante
                                                WHERE id_hoja_ruta = ?
                                                AND fecha_baja is null";

                }else if($userRol=="ROL_API_MN_PO"){

                    $sql_get_items_hoja_ruta = "SELECT id, id_hoja_ruta, id_provincia, id_linea, id_almacen,
                                                       id_tipo_material, id_grupo_material, id_estado_conservacion
                                                FROM item_hoja_ruta_nuevo_producido
                                                WHERE id_hoja_ruta = ?
                                                AND fecha_baja is null";

                }
                //1

                $stihr = $em_inventario->getConnection()->prepare($sql_get_items_hoja_ruta);
                $stihr->bindValue(1, $ruta[$key]);
                $stihr->execute();

                $items = $stihr->fetchAll();

                foreach ($items as $item){

                    if($userRol=="ROL_API_AL"){

                        $key='id_activo_lineal';
                        $sql_get_mateial_data = "SELECT id_operador, id_linea, id_corredor, id_division,
                                                        id_ramal, id_categorizacion, id_estado_conservacion, id_tipo_via,
                                                        id_tipo_activo, id_estacion, progresiva_inicio_tramo,
                                                        progresiva_final_tramo, zona_via
                                                 FROM activo_lineal
                                                 WHERE id = ?
                                                 AND fecha_baja is null";

                    }else if($userRol=="ROL_API_MR"){

                        $key='id_material_rodante';
                        $sql_get_mateial_data = "SELECT id, denominacion, id_grupo_rodante, id_tipo_rodante,
                                                        id_marca, id_modelo, id_estado_conservacion,
                                                        id_estado_servicio, id_linea, id_operador, id_codigo_trafico,
                                                        id_estacion, numero_vehiculo, latitud, longitud
                                                  FROM catalogo_material_rodante
                                                  WHERE id = ?
                                                  AND fecha_baja is null";

                    }

                    if($userRol!="ROL_API_MN_PO"){
                      $stmat = $em_inventario->getConnection()->prepare($sql_get_mateial_data);
                      $stmat->bindValue(1, $item[$key]);
                      $stmat->execute();

                      $material_data = $stmat->fetchAll();
                    }

                    if($userRol=="ROL_API_AL"){

                        $sql_get_atributos = "SELECT t1.id_valor_atributo as id, t2.denominacion as valor
                                              FROM activo_lineal_atributo_valor t1, valores_atributo t2
                                              WHERE t1.id_activo_lineal = ?
                                              AND t1.id_valor_atributo = t2.id
                                              AND t2.fecha_baja is null";

                        $statr = $em_inventario->getConnection()->prepare($sql_get_atributos);
                        $statr->bindValue(1, $item[$key]);
                        $statr->execute();

                        $atributos = $statr->fetchAll();

                        $key='atributos';
                        $material_data[0][$key] = $atributos;

                    }

                    if($userRol=="ROL_API_AL"){
                        $key='activo_lineal';
                    }else if($userRol=="ROL_API_MR"){
                        $key='material_rodante';
                    }

                    if($userRol!="ROL_API_MN_PO"){
                        $item[$key] = $material_data;
                    }

                    array_push($items_final, $item);
                }

                if($userRol=="ROL_API_AL"){
                    $key='items_activo_lineal';
                }else if($userRol=="ROL_API_MR"){
                    $key='items_material_rodante';
                }else{
                    $key='items_mat_nuevo_producido_obra';
                }

                $ruta[$key] = $items_final;
                $items_final = array();

                array_push($rutas_final, $ruta);
            }

            $myresponse = array('HojaDeRuta' => array($rutas_final));

                /*if($rutaID!=""){
                    $sql_update = "UPDATE API_UpdateTables
                                   SET download = 1
                                   WHERE user_id = ? and hoja_ruta_id = ?";

                    $strus = $em_user->getConnection()->prepare($sql_update);
                    $strus->bindValue(1, $userId);
                    $strus->bindValue(2, $rutaID);
                    $strus->execute();
                }
                else{

                    $sql_update = "UPDATE API_UpdateTables
                                   SET download = 1
                                   WHERE user_id= ?";

                    $strus = $em_user->getConnection()->prepare($sql_update);
                    $strus->bindValue(1, $userId);
                    $strus->execute();

                    $myresponse = array('HojaDeRuta' => array($rutas));
              }*/

              $myresponse = array(
                  "content" => $myresponse
              );

              $finalResponse = json_encode($myresponse);
              $response = new Response($finalResponse);
              $response->headers->set('Content-Type', 'application/json');
              return $response;
        }
        else{

            $myresponse = array(
                "content" => array('error' => "Unauthorized Role")
            );

            $finalResponse = json_encode($myresponse);
            $response = new Response($finalResponse);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }







    /**************************************************************************************************************/
    /**************************************************************************************************************/
    /**
    * @Route("/hoja_ruta_status", name="hoja_de_ruta_status")
    * @Method("GET")
    * @param Request $request
    * @return Response
    */
    public function getHojaRutaStatus(Request $request)
    {
        $request = $this->getRequest();
        $userId = $request->query->get('userId');
        $userRol = $request->query->get('userRol');
        $deviceId = $request->query->get('deviceId');
        $tokenId = $request->query->get('tokenId');

        $em_user = $this->getDoctrine()->getManager('siga_autenticacion');
        $em_inventario = $this->getDoctrine()->getManager('adif_inventario');

        $sql_validar = "SELECT count(*)
                        FROM usuario as us, AccessToken as t, usuario_grupo_empresa as uge, grupo as g
                        WHERE us.id= ?
                        AND t.token = ?
                        AND t.user_id = us.id
                        AND uge.id_usuario = us.id
                        AND uge.id_grupo = g.id
                        AND g.roles like ? ";

        $stus = $em_user->getConnection()->prepare($sql_validar);
        $stus->bindValue(1, $userId);
        $stus->bindValue(2, $tokenId);
        $stus->bindValue(3, '%'.$userRol.'%');
        $stus->execute();

        $sqlVal = $stus->fetchColumn();

        $rutas = array ();

        if($sqlVal != "0"){

            $sql_rutas = "SELECT hoja_ruta_id as id
                          FROM API_UpdateTables
                          WHERE user_id = ? AND download = 0";

            $ra = $em_user->getConnection()->prepare($sql_rutas);
            $ra->bindValue(1, $userId);
            $ra->execute();

            array_push($rutas, $ra->fetchAll());

            $myresponse = array(
                "content" => $rutas
            );

            $finalResponse = json_encode($myresponse);
            $response = new Response($finalResponse);
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }
        else{

            $myresponse = array(
                "content" => array('error' => "Unauthorized Role")
            );

            $finalResponse = json_encode($myresponse);
            $response = new Response($finalResponse);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }






    /**************************************************************************************************************/
    /**************************************************************************************************************/
    /**
    * @Route("/reset_hoja_ruta_status", name="reset_hoja_de_ruta_status")
    * @Method("POST")
    * @return Response
    */
    public function resetHojaRutaStatus()
    {

        $em_user = $this->getDoctrine()->getManager('siga_autenticacion');

        $sql_update = "UPDATE API_UpdateTables
                       SET download = 0";

        $strus = $em_user->getConnection()->prepare($sql_update);
        $strus->execute();

        $myresponse = array(
          "content" => "Reset Complete"
        );

        $finalResponse = json_encode($myresponse);
        $response = new Response($finalResponse);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
