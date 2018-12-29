<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


/**
 *
 */
class CatalogoMaterialesRodantesRepository extends EntityRepository {


    public function findAllMaterialeRodante()
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('grupoRodante', 'grupoRodante');
        $rsm->addScalarResult('tipoRodante', 'tipoRodante');
        $rsm->addScalarResult('numeroVehiculo', 'numeroVehiculo');
        $rsm->addScalarResult('marca', 'marca');
        $rsm->addScalarResult('modelo', 'modelo');
        $rsm->addScalarResult('estadoConservacion', 'estadoConservacion');
        $rsm->addScalarResult('estadoServicio', 'estadoServicio');
        $rsm->addScalarResult('codigoTrafico', 'codigoTrafico');
        $rsm->addScalarResult('linea', 'linea');
        $rsm->addScalarResult('operador', 'operador');
        $rsm->addScalarResult('ubicacion', 'ubicacion');
        $rsm->addScalarResult('estadoInventario', 'estadoInventario');

        $native_query = $this->getEntityManager()->createNativeQuery('SELECT * FROM vista_materiales_rodantes', $rsm);
        $result = $native_query->getResult();

        return $result;
    }

    public function getItems($datos){

        $sql = 'SELECT mr.id, IDENTITY(mr.idOperador) as operador, IDENTITY(mr.idLinea) as linea, IDENTITY(mr.idEstacion) as estacion,'
            . 'IDENTITY(mr.idGrupoRodante) as grupoRodante, IDENTITY(mr.idTipoRodante) as tipoRodante,'
            . ' mr.numeroVehiculo as numeroVehiculo ';

        $sql .= 'FROM ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes mr ';
        $sql .= 'LEFT JOIN mr.itemsHojaRutaMaterialRodante i ';
        $sql .= 'LEFT JOIN i.hojaRuta hr WITH ( hr.id = i.hojaRuta AND hr.estadoHojaRuta <> 3 AND hr.fechaVencimiento > CURRENT_DATE() ) ';
        $sql .= 'WHERE ';

        foreach($datos as $key => $id){
            //$campo = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            $sql .= "mr.$key = '$id' AND ";
        }

        $sql .= ' hr.id IS NULL';
        $query = $this->getEntityManager()->createQuery($sql);
            return $query->getResult();

    }

    public function getMaterialRodante($ids)
    {
        $queryBuilder = $this->createQueryBuilder('c')
                ->select('c.id', 'c.numeroVehiculo');
        foreach($ids as $key => $id){

            $key= ($key == 'tipoRodante')? 'idTipoRodante' : $key;
            $key= ($key == 'operador')? 'idOperador' : $key;
            $key= ($key == 'linea')? 'idLinea' : $key;
            $key= ($key == 'estacion')? 'idEstacion' : $key;
            $key= ($key == 'grupoRodante')? 'idGrupoRodante' : $key;
            
            $queryBuilder
                    ->andWhere("c.$key = :$key")
                    ->andWhere("c.idEstadoInventario = 2")
                    ->setParameter($key,$id);
        }

        $query = $queryBuilder
            ->addOrderBy('c.numeroVehiculo', 'ASC')
            ->getQuery();

        return $query->getResult();
    }


}
