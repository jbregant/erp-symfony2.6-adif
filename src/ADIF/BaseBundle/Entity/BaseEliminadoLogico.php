<?php

namespace ADIF\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @author Manuel Becerra
 * created 03/07/2014
 * 
 * BaseEliminadoLogico. 
 * 
 * Aquella clase que extienda de BaseEliminadoLogico eliminará de forma lógica a 
 * sus entidades.
 * 
 * @ORM\MappedSuperclass 
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 */
class BaseEliminadoLogico /* implements \Serializable */{

    /**
     * @var \DateTime $eliminado
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    protected $fechaBaja;

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja() {
        return $this->fechaBaja;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return BaseEliminadoLogico
     */
    public function setFechaBaja($fechaBaja) {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }
    
    
    /**
     * 
     * @return type
     */
//    public function serialize() {
//        return serialize($this->getId());
//    }

    /**
     * 
     * @param type $serialized
     */
//    public function unserialize($serialized) {
//        $this->id = unserialize($serialized);
//    }
    
}
