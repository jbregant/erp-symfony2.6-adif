<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 */
class AlmacenRepository extends EntityRepository {

    public function findAllAlmacenes()
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('denominacion', 'denominacion');
        $rsm->addScalarResult('tipo', 'tipo');
        $rsm->addScalarResult('numeroDeposito', 'numeroDeposito');
        $rsm->addScalarResult('provincia', 'provincia');
        $rsm->addScalarResult('latitud', 'latitud');
        $rsm->addScalarResult('longitud', 'longitud');
        $rsm->addScalarResult('linea', 'linea');
        $rsm->addScalarResult('estacion', 'estacion');
        $rsm->addScalarResult('zonaVia', 'zonaVia');

        $native_query = $this->getEntityManager()->createNativeQuery('SELECT * FROM vista_almacenes', $rsm);
        $result = $native_query->getResult();

        return $result;
    }

    public function findByProvinciaYLinea($idProvincia, $idLinea) {

            $linea = $this->getEntityManager()->getRepository('ADIFInventarioBundle:Linea')->find($idLinea);

            $repository = $this->getEntityManager()
                    ->getRepository('ADIFInventarioBundle:Almacen', $this->getEntityManager());

            if(!empty($linea) && !empty($idProvincia)){
                $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.idProvincia = :provincia AND c.linea = :linea')
                    ->setParameter('provincia', $idProvincia)
                    ->setParameter('linea', $linea)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();
            }else if(!empty($linea)){
                 $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.linea = :linea')
                    ->setParameter('linea', $linea)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();
            }else if(!empty($idProvincia)){
                $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.idProvincia = :provincia')
                    ->setParameter('provincia', $idProvincia)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();
            }

            return $query->getResult();
    }


}
