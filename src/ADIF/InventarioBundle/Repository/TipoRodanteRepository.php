<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 */
class TipoRodanteRepository extends EntityRepository {

    /**
     *
     * @param integer $id
     * @return tiporRodante
     */
    public function findTipoRodantebyRodante($id) {
        $rodante = $this->getEntityManager()->getRepository('ADIFInventarioBundle:GrupoRodante')->find($id);
        $repository = $this->getEntityManager()
                ->getRepository('ADIFInventarioBundle:TipoRodante', $this->getEntityManager());

        $query = $repository->createQueryBuilder('c')
                ->select('c.id', 'c.denominacion')
                ->where('c.grupoRodante =  :rodante')
                ->setParameter('rodante', $rodante)
                ->orderBy('c.denominacion', 'ASC')
                ->getQuery();

        return ($query->getResult());
    }


}
