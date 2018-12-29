<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 */
class ActivoLinealRepository extends EntityRepository {

    /**
     *
     * @param integer $id
     * @return type
     */
    public function findByIdsFormated($ids) {

        $results = $this->findBy(['id' => $ids],['progresivaInicioTramo' => 'ASC']);
        $activosLineales = [];
        foreach($results as $result){
            $activoLineal['progresivaInicioTramo'] = $result->getProgresivaInicioTramo();
            $activoLineal['progresivaFinalTramo'] = $result->getProgresivaFinalTramo();
            $activoLineal['datos']['linea'] = $result->getLinea()->getId();
            $activoLineal['datos']['operador'] = $result->getOperador()->getId();
            $activoLineal['datos']['division'] = $result->getDivision()->getId();
            $activoLineal['estadoConservacion'] = $result->getEstadoConservacion();
            $activoLineal['tipoActivo'] = $result->getTipoActivo()->getDenominacion();
            foreach ($result->getValoresAtributo()->toArray() as $value) {
                $activoLineal['atributos'][$value->getAtributo()->getDenominacion()] = $value->getDenominacion();
            }

            $activosLineales[] = $activoLineal;
        }

        return $activosLineales ;
    }

    public function getAtributoValue($id, $atributo){

      $sql = 'SELECT t2.denominacion
              FROM activo_lineal_atributo_valor t1, valores_atributo t2, atributo t3
              WHERE t1.id_activo_lineal = :id
              AND t1.id_valor_atributo = t2.id
              AND t2.id_atributo = t3.id
              AND t3.denominacion like :at';
      $params = array('id' => $id, 'at' => $atributo);

      $values = $this->getEntityManager()->getConnection()->executeQuery($sql, $params);
      return ($values->fetchColumn(0));
    }

    public function findProgresivaFinalTramo($datos , $entity = null){
        if(!$datos){
            $datos = [
                'linea' => $entity->getLinea(),
                'operador' => $entity->getOperador(),
                'corredor' => $entity->getCorredor(),
                'division' => $entity->getDivision(),
                'tipoVia' => $entity->getTipoVia(),
                'tipoActivo' => $entity->getTipoActivo()
            ];
        }

        $queryBuilder = $this->createQueryBuilder('a')
            ->select('MAX(a.progresivaFinalTramo)');

        foreach($datos as $key => $id){
            $nombre = ($key === 'division')?'divisiones':$key;
            $entity = $this->getEntityManager()->getRepository('ADIFInventarioBundle:'.ucfirst($nombre))->find($id);
            $queryBuilder
                ->andWhere("a.$key = :$key")
                ->setParameter($key,$entity);
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function findProgresivas($datos){
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a.id, a.progresivaInicioTramo, a.progresivaFinalTramo');

        foreach($datos as $key => $id){
            $nombre = ($key === 'division')?'divisiones':$key;
            $entity = $this->getEntityManager()->getRepository('ADIFInventarioBundle:'.ucfirst($nombre))->find($id);
            $queryBuilder
                ->andWhere("a.$key = :$key")
                ->setParameter($key,$entity);
        }

        $activosLineales = $queryBuilder->getQuery()->getArrayResult();

        $result = [
            'progresivaInicioTramo' => [],
            'progresivaFinalTramo' => []
        ];
        foreach($activosLineales as $activoLineal){
            $result['progresivaInicioTramo'][$activoLineal['id']] = $activoLineal['progresivaInicioTramo'];
            $result['progresivaFinalTramo'][$activoLineal['id']] = $activoLineal['progresivaFinalTramo'];
        }
        return $result;
    }

    public function findAllactivoLineal()
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('operador', 'operador');
        $rsm->addScalarResult('linea', 'linea');
        $rsm->addScalarResult('division', 'division');
        $rsm->addScalarResult('corredor', 'corredor');
        $rsm->addScalarResult('ramal', 'ramal');
        $rsm->addScalarResult('categoria', 'categoria');
        $rsm->addScalarResult('progresivaInicioTramo', 'progresivaInicioTramo');
        $rsm->addScalarResult('progresivaFinalTramo', 'progresivaFinalTramo');
        $rsm->addScalarResult('tipoVia', 'tipoVia');
        $rsm->addScalarResult('kilometraje', 'kilometraje');
        $rsm->addScalarResult('estadoConservacion', 'estadoConservacion');
        $rsm->addScalarResult('balasto', 'balasto');
        $rsm->addScalarResult('rieles', 'rieles');
        $rsm->addScalarResult('rieles', 'rieles');
        $rsm->addScalarResult('durmientes', 'durmientes');
        $rsm->addScalarResult('velocidad', 'velocidad');
        $rsm->addScalarResult('capacidad', 'capacidad');
        $rsm->addScalarResult('tipoActivo', 'tipoActivo');
        $rsm->addScalarResult('estacion', 'estacion');
        $rsm->addScalarResult('estadoInventario', 'estadoInventario');
        $rsm->addScalarResult('zonaVia', 'zonaVia');
        $rsm->addScalarResult('tipoServicio', 'tipoServicio');

        $native_query = $this->getEntityManager()->createNativeQuery('SELECT * FROM vista_activos_lineales', $rsm);
        $result = $native_query->getResult();

        return $result;
    }
    
    public function getItems($datos){
       /* $sql = 'SELECT a.id, a.id_linea, a.id_operador, a.id_division, a.id_tipo_activo, a.progresiva_inicio_tramo, a.progresiva_final_tramo ';
        $sql .= 'FROM activo_lineal a ';
        $sql .= 'LEFT JOIN item_hoja_ruta_activo_lineal i ON i.id_activo_lineal = a.id ';
        $sql .= 'LEFT JOIN hoja_ruta hr ON ( hr.id = i.id_hoja_ruta AND hr.id_estado_hoja_ruta <> 3 AND hr.fecha_vencimiento > CURRENT_DATE() ) ';
        $sql .= 'WHERE ';
        foreach($datos as $key => $id){
            $campo = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            $sql .= "a.$campo = $id AND ";
        }
        $sql .= ' hr.id IS NULL';
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('id_operador', 'operador');
        $rsm->addScalarResult('id_linea', 'linea');
        $rsm->addScalarResult('id_division', 'division');
        $rsm->addScalarResult('progresiva_inicio_tramo', 'progresivaInicioTramo');
        $rsm->addScalarResult('progresiva_final_tramo', 'progresivaFinalTramo');
        $rsm->addScalarResult('id_tipo_activo', 'tipoActivo');
        $query = $this->getEntityManager()->createNativeQuery($sql,$rsm);*/
        
        $sql = 'SELECT a.id, IDENTITY(a.linea) as linea, IDENTITY(a.operador) as operador, '
            . 'IDENTITY(a.division) as division, IDENTITY(a.tipoActivo) as tipoActivo, a.progresivaInicioTramo, a.progresivaFinalTramo ';
        $sql .= 'FROM ADIF\InventarioBundle\Entity\ActivoLineal a ';
        $sql .= 'LEFT JOIN a.itemsHojaRutaActivoLineal i ';
        $sql .= 'LEFT JOIN i.hojaRuta hr WITH ( hr.id = i.hojaRuta AND hr.estadoHojaRuta <> 3 AND hr.fechaVencimiento > CURRENT_DATE() ) ';
        $sql .= 'WHERE ';
        foreach($datos as $key => $id){
            //$campo = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            $sql .= "a.$key = $id AND ";
        }
        $sql .= ' hr.id IS NULL';
        $query = $this->getEntityManager()->createQuery($sql);
        return $query->getResult();
    }
}
