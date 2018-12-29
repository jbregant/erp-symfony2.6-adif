<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LicitacionChatarra
 *
 * @author Manuel Becerra
 * created 25/03/2015
 * 
 * @ORM\Table(name="licitacion_chatarra")
 * @ORM\Entity 
 */
class LicitacionChatarra extends Licitacion {

    /**
     * @ORM\Column(name="id_cliente", type="integer", nullable=true)
     */
    protected $idCliente;

    /**
     * @var ADIF\ComprasBundle\Entity\Cliente
     */
    protected $cliente;

    /**
     * @param type $idCliente
     * @return LicitacionChatarra
     */
    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
        return $this;
    }

    /**
     * Get idCliente
     *
     * @return integer 
     */
    public function getIdCliente() {
        return $this->idCliente;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Cliente $cliente
     */
    public function setCliente($cliente) {
        if (null != $cliente) {
            $this->idCliente = $cliente->getId();
        } else {
            $this->idCliente = null;
        }

        $this->cliente = $cliente;
    }

    /**
     * 
     * @return type
     */
    public function getCliente() {
        return $this->cliente;
    }

}
