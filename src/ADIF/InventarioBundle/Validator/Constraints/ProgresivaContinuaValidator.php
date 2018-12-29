<?php
namespace ADIF\InventarioBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use ADIF\InventarioBundle\Entity\ActivoLineal;
use ADIF\InventarioBundle\Entity\TipoActivo;
use Doctrine\Common\Util\Debug;

class ProgresivaContinuaValidator extends ConstraintValidator
{
    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    public function validate($entity, Constraint $constraint)
    {
        $error = 'not';
        $tipoTramoAnterior = '';
        $tipoActivo = $entity->getTipoActivo();
        if($tipoActivo == null){
            return false;
        }

        $entity->getId() != null ? $acID = $entity->getId() : $acID = 0;
        ($tipoActivo->getDenominacion() != 'Vía' && $tipoActivo->getDenominacion() != 'Tendido Fibra Óptica') ? $valVar = "!=" : $valVar = "=";

        $datos = [
            'linea' => $entity->getLinea()->getId(),
            'operador' => $entity->getOperador()->getId(),
            'corredor' => $entity->getCorredor()->getId(),
            'division' => $entity->getDivision()->getId(),
            'tipoVia' => $entity->getTipoVia()->getId(),
        ];

        $query = $this->doctrine->getRepository('ADIFInventarioBundle:ActivoLineal','adif_inventario')
                ->createQueryBuilder('acl')
                ->select('acl.id')
                ->where('acl.id != :id and acl.progresivaInicioTramo = :pro_i
                         and acl.progresivaFinalTramo = :pro_f and acl.linea = :linea
                         and acl.operador = :operador and acl.corredor = :corredor
                         and acl.division = :division
                         and acl.tipoVia = :tipoVia
                         and acl.tipoActivo '.$valVar.' :tipoActivo')
                ->setParameter('id', $acID)
                ->setParameter('pro_i', $entity->getProgresivaInicioTramo())
                ->setParameter('pro_f', $entity->getProgresivaFinalTramo())
                ->setParameter('linea', $entity->getLinea()->getId())
                ->setParameter('operador', $entity->getOperador()->getId())
                ->setParameter('corredor', $entity->getCorredor()->getId())
                ->setParameter('division', $entity->getDivision()->getId())
                ->setParameter('tipoVia', $entity->getTipoVia()->getId())
                ->setParameter('tipoActivo', $entity->getTipoActivo()->getId())
                ->getQuery();
        $list = $query->getResult();
        $activosQty = count($list);

        if($activosQty > 0){
            //var_dump("Error A"); die();
            $error = "y1";
        }


        if($error == 'not'){
            if($tipoActivo->getDenominacion() != 'Vía' && $tipoActivo->getDenominacion() != 'Tendido Fibra Óptica')
            {
                $progresiva_i = false;
                $progresiva_f = false;
                $datos_ta = ['denominacion' => 'Vía'];

                /*$tipoVia = $this->doctrine->getRepository('ADIFInventarioBundle:TipoActivo','adif_inventario')
                    ->findBy($datos_ta);

                var_dump($tipoVia[0]->getId()); die();

                $datos_add_i = [
                  'progresivaInicioTramo' => $entity->getProgresivaInicioTramo(),
                  'tipoActivo' => $tipoVia->getId()
                ];

                $datos_add_f = [
                  'progresivaFinalTramo' => $entity->getProgresivaFinalTramo(),
                  'tipoActivo' => $tipoVia->getDenominacion()->getId()
                ];*/

                $datos_add_i = ['progresivaFinalTramo' => $entity->getProgresivaInicioTramo()];
                $datos_add_f = ['progresivaInicioTramo' => $entity->getProgresivaFinalTramo()];

                $datos_i = $datos + $datos_add_i;
                $datos_f = $datos + $datos_add_f;

                $activosLineales_i = $this->doctrine->getRepository('ADIFInventarioBundle:ActivoLineal','adif_inventario')
                    ->findBy($datos_i);

                $activosLineales_f = $this->doctrine->getRepository('ADIFInventarioBundle:ActivoLineal','adif_inventario')
                    ->findBy($datos_f);

                foreach($activosLineales_i as $activoLineal_i){
                    if($entity->getProgresivaInicioTramo() == $activoLineal_i->getProgresivaFinalTramo()){
                        $progresiva_i = true;
                    }
                }

                if($progresiva_i){
                   foreach($activosLineales_f as $activoLineal_f){
                      if($entity->getProgresivaFinalTramo() == $activoLineal_f->getProgresivaInicioTramo()){
                         $progresiva_f = true;
                      }
                   }

                   if($entity->getProgresivaFinalTramo() == "0.000"){
                      $progresiva_f = true;
                   }
                }

                if($progresiva_i == false || $progresiva_f == false){
                    $error = 'y2';
                }
            }
            else{

                $datos_add = ['tipoActivo' => $entity->getTipoActivo()->getId()];
                $datos_search = $datos + $datos_add;

                $activosLineales = $this->doctrine->getRepository('ADIFInventarioBundle:ActivoLineal','adif_inventario')
                    ->findBy($datos_search,['progresivaInicioTramo' => 'ASC', 'progresivaFinalTramo' => 'ASC']);

                /*La primer progresiva debe ser 0:
                if($activosLineales[0]->getProgresivaInicioTramo() == 0.000){
                    $error = true;
                }*/

                foreach($activosLineales as $activoLineal){

                    //Cuando consulto con la entity que se está modificando
                    if( $activoLineal->getId() == $entity->getId() ){
                        $activoLineal = $entity; //La reemplazo por la modificada para comparar
                    }

                    //Valido que sea continua
                    if(isset($finTramoAnterior) && $activoLineal->getProgresivaInicioTramo() != $finTramoAnterior){
                        //var_dump("A.B"); die();
                        $error = 'y1';
                    }

                    $finTramoAnterior = $activoLineal->getProgresivaFinalTramo();
                    $tipoTramoAnterior = $activoLineal->getTipoActivo();
                }

                //Si es nueva valido que sea contigua al último tramo:
                $finTramoAnterior = isset($finTramoAnterior)?$finTramoAnterior:0;

                if($entity->getId() == null && $entity->getProgresivaInicioTramo() != $finTramoAnterior){
                    //var_dump("C.D"); die();
                    $error = 'y1';
                }
            }
        }

        //var_dump($error); die();

        if($error != 'not'){
            if($error == 'y2'){
                $this->context->buildViolation($constraint->message1)->addViolation();
            }
            else{
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
