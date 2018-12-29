<?php

namespace ADIF\ContableBundle\Entity\Constantes;

/**
 * Description of ConstanteEstadoOrdenPago
 *
 * @author Manuel Becerra
 * created 29/11/2014
 */
class ConstanteEstadoOrdenPago {

    /**
     * ESTADO_PENDIENTE_AUTORIZACION
     */
    const ESTADO_PENDIENTE_AUTORIZACION = "Pendiente autorización";

    /**
     * ESTADO_PENDIENTE_PAGO
     */
    const ESTADO_PENDIENTE_PAGO = "Pendiente pago";

    /**
     * ESTADO_PAGADA
     */
    const ESTADO_PAGADA = "Pagada";

    /**
     * ESTADO_ANULADA
     */
    const ESTADO_ANULADA = "Anulada";

    /**
     * ___ESTADO_OP_ASIGNADA
     */
    const ___ESTADO_OP_ASIGNADA = "OP asignada";
    
    /**
     * ___ESTADO_OP_ASIGNADA
     */
    const ESTADO_NETCASH_CORRIDA_PENDIENTE = "Net Cash corrida pendiente";

}
