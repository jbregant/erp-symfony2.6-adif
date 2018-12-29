<?php

namespace ADIF\RecursosHumanosBundle\Entity\Constantes;

use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Description of ConstanteSirhu
 *
 * @author Andros Garavaglia
 * created 03/11/2014
 */
class ConstanteSirhu
{

    const INDEX_GRADO = 14;
    const INDEX_ESCALAFON = 15;
    
    private static $em = null;
    
    protected static $vec = array(
                                array('M'=>'MAS','F'=>'FEM'), //Codigos de sexo 0
                                array('1'=>'1','2'=>'3','3'=>'3','4'=>'3','5'=>'3'), //Codigos de nacionalidad 1
                                array('1'=>'T','2'=>'T','3'=>'P'), //Tipo de Planta 2
                                array('1'=>'DNI'), //Codigos de tipo de documento 3
                                array('0'=>'0'), //Codigos de tipo de unidad fisica 4
                                array('1'=>'1','2'=>'1','3'=>'2','4'=>'4','5'=>'2','6'=>'2','7'=>'4','8'=>'2'), //Codigos de tipo de concepto 5
                                array('1'=>'ES','2'=>'ET','3'=>'EU','4'=>'EP'), //Codigos de educacion 6
                                array('0'=>'0'), //Tipo de horario 7
                                array('0'=>'0'), //Sistema previsional 8
                                array('0'=>'0'), //Remunerativo/Bonificable 9
                                array('1'=>'SOL','2'=>'CAS','3'=>'DIV','4'=>'VIU','5'=>'SEP','6'=>'UHE'), //Codigos estado civil 10
                                array('1'=>'ES','2'=>'S','3'=>'S','4'=>'N'), //Marca de gradudado 11
                                array('1'=>'A','2'=>'A','3'=>'A','4'=>'A','5'=>'L','6'=>'A'), //Marca Estado Licencia 12
                                array('1'=>'1','2'=>'3'), //Remunerativo/Bonificable 13
                                array('2'=>'2','3'=>'1','4'=>'12','5'=>'11','6'=>'7','7'=>'6','8'=>'5','9'=>'4','10'=>'10','11'=>'9','12'=>'8',
                                        '13'=>'3','14'=>'2','15'=>'1','16'=>'31','17'=>'30','18'=>'29','19'=>'28','20'=>'27','21'=>'26','22'=>'25',
                                        '23'=>'24','24'=>'23','25'=>'22','26'=>'21','27'=>'20','28'=>'19','29'=>'18','30'=>'17','31'=>'16','32'=>'15',
                                        '33'=>'14','34'=>'13','35'=>'12','36'=>'11','37'=>'10','38'=>'9','39'=>'8','40'=>'7','41'=>'6','42'=>'5','43'=>'4',
                                        '44'=>'3',
										
										// Apartir de la subcategoria.id >= 47 son todos los "fuera de convenio" y va grado 309
										'47'=>'309', // Fuera de convenio A
										'48'=>'309', // Director
										'50'=>'309', // Fuera de Convenio B
										'51'=>'309', // Fuera de Convenio C
										'52'=>'309', //	Fuera de Convenio D
										'53'=>'309', // Fuera de Convenio E
										'54'=>'309', // Fuera de Convenio F
										'55'=>'309', // Fuera de Convenio G
										'56'=>'309', // Fuera de Convenio H
										'57'=>'309', // Fuera de Convenio I
										'58'=>'309', // Fuera de Convenio J
										'59'=>'309', // Fuera de Convenio K
										'60'=>'309', // Fuera de Convenio L 
										'61'=>'309', // Fuera de Convenio M
                                        '62'=>'309', // Fuera de Convenio Gerente 1
										'63'=>'309', // Fuera de Convenio Subgerente 1
										'64'=>'309', // SUBGERENTE 1
										'65'=>'309', // GERENTE 1
										'66'=>'309', // Fuera de convenio GERENTE
										'67'=>'309', // Fuera de convenio SUBGERENTE
										'68'=>'309', // Fuera de convenio GERENTE 1
										'69'=>'309', // Fuera de convenio SUBGERENTE 1
                                        '70'=>'309', // GERENTE A - ASESOR PRESIDENCIA
										'71'=>'309', // GERENTE B - GERENTE AC
										'72'=>'309', // GERENTE C - GERENTE CI Y RRII
										'73'=>'309', // GERENTE D - GERENTE PROYECTO
										'74'=>'309', // GERENTE E - GERENTE SP Y SG
										'75'=>'309', // GERENTE F - ASESOR PRESIDENCIA
                                        '76'=>'309', // GERENTE G - GERENTE AyL
										'77'=>'309', // GERENTE H - ASESOR PRESIDENCIA
										'78'=>'309', // GERENTE I - GERENTE CP
										'79'=>'309', // GERENTE J - GERENTE AyF
										'80'=>'309', // GERENTE K - ASESOR PRESIDENCIA
										'81'=>'309', // GERENTE L - GERENTE RH y RL
                                        '82'=>'309', // GERENTE M - GERENTE T,I y P
										'83'=>'309', // GERENTE N - GERENTE IyC
										'84'=>'309', // Asesor Fuera de Convenio B - ASESOR FC
										'85'=>'309', // Asesor Fuera de Convenio A - ASESOR FC
										'86'=>'309', // GERENTE O - GERENTE PROYECTO
										'87'=>'309', // GERENTE P - GERENTE O. CIVILES
										'88'=>'309', // GERENTE Q - GERENTE O.VIAS
										'89'=>'309', // GERENTE R - GERENTE O. ENERGIA
										'90'=>'309', // GERENTE S - GERENTE O. SEÑALAMIENTO
										'91'=>'309', // GERENTE T - GERENTE ADM.CONTRATOS
										'92'=>'309', // GERENTE U - GERENTE A.F.
										
										'93'=>'309', // GERENTE V - GERENTE UAI
										'94'=>'309', // GERENTE W - GERENTE P.E.
										'95'=>'309', // GERENTE X - GERENTE DE PROYECTOS B.C.V
										'96'=>'309', // GERENTE Y - GERENTE O.CIVILES
										'97'=>'309', // GERENTE Z - GERENTE DE OPERACIONES I&C
										'98'=>'309', // GERENTE A1 - GERENTE
										'99'=>'309', // Asesor Fuera de Convenio C - ASESOR FC
										
                                    ), //Codigo de grado 14
                                array('2'=>'397','3'=>'397','4'=>'396','5'=>'396','6'=>'396','7'=>'396','8'=>'396','9'=>'396','10'=>'396','11'=>'396','12'=>'396',
                                      '13'=>'396','14'=>'396','15'=>'396','16'=>'397','17'=>'397','18'=>'397','19'=>'397','20'=>'397','21'=>'397','22'=>'397','23'=>'397',
                                      '24'=>'397','25'=>'397','26'=>'397','27'=>'397','28'=>'397','29'=>'397','30'=>'397','31'=>'397','32'=>'397','33'=>'397','34'=>'397',
                                      '35'=>'397','36'=>'397','37'=>'397','38'=>'397','39'=>'397','40'=>'397','41'=>'397','42'=>'397','43'=>'397','44'=>'397',
                                      
										// Apartir de la subcategoria.id >= 47 son todos los "fuera de convenio" y va escalafon 103
										'47'=>'103', // Fuera de convenio A
										'48'=>'103', // Director
										'50'=>'103', // Fuera de Convenio B
										'51'=>'103', // Fuera de Convenio C
										'52'=>'103', //	Fuera de Convenio D
										'53'=>'103', // Fuera de Convenio E
										'54'=>'103', // Fuera de Convenio F
										'55'=>'103', // Fuera de Convenio G
										'56'=>'103', // Fuera de Convenio H
										'57'=>'103', // Fuera de Convenio I
										'58'=>'103', // Fuera de Convenio J
										'59'=>'103', // Fuera de Convenio K
										'60'=>'103', // Fuera de Convenio L 
										'61'=>'103', // Fuera de Convenio M
                                        '62'=>'103', // Fuera de Convenio Gerente 1
										'63'=>'103', // Fuera de Convenio Subgerente 1
										'64'=>'103', // SUBGERENTE 1
										'65'=>'103', // GERENTE 1
										'66'=>'103', // Fuera de convenio GERENTE
										'67'=>'103', // Fuera de convenio SUBGERENTE
										'68'=>'103', // Fuera de convenio GERENTE 1
										'69'=>'103', // Fuera de convenio SUBGERENTE 1
                                        '70'=>'103', // GERENTE A - ASESOR PRESIDENCIA
										'71'=>'103', // GERENTE B - GERENTE AC
										'72'=>'103', // GERENTE C - GERENTE CI Y RRII
										'73'=>'103', // GERENTE D - GERENTE PROYECTO
										'74'=>'103', // GERENTE E - GERENTE SP Y SG
										'75'=>'103', // GERENTE F - ASESOR PRESIDENCIA
                                        '76'=>'103', // GERENTE G - GERENTE AyL
										'77'=>'103', // GERENTE H - ASESOR PRESIDENCIA
										'78'=>'103', // GERENTE I - GERENTE CP
										'79'=>'103', // GERENTE J - GERENTE AyF
										'80'=>'103', // GERENTE K - ASESOR PRESIDENCIA
										'81'=>'103', // GERENTE L - GERENTE RH y RL
                                        '82'=>'103', // GERENTE M - GERENTE T,I y P
										'83'=>'103', // GERENTE N - GERENTE IyC
										'84'=>'103', // Asesor Fuera de Convenio B - ASESOR FC
										'85'=>'103', // Asesor Fuera de Convenio A - ASESOR FC
										'86'=>'103', // GERENTE O - GERENTE PROYECTO
										'87'=>'103', // GERENTE P - GERENTE O. CIVILES
										'88'=>'103', // GERENTE Q - GERENTE O.VIAS
										'89'=>'103', // GERENTE R - GERENTE O. ENERGIA
										'90'=>'103', // GERENTE S - GERENTE O. SEÑALAMIENTO
										'91'=>'103', // GERENTE T - GERENTE ADM.CONTRATOS
										'92'=>'103', // GERENTE U - GERENTE A.F.
										
										'93'=>'103', // GERENTE V - GERENTE UAI
										'94'=>'103', // GERENTE W - GERENTE P.E.
										'95'=>'103', // GERENTE X - GERENTE DE PROYECTOS B.C.V
										'96'=>'103', // GERENTE Y - GERENTE O.CIVILES
										'97'=>'103', // GERENTE Z - GERENTE DE OPERACIONES I&C
										'98'=>'103', // GERENTE A1 - GERENTE
										'99'=>'103', // Asesor Fuera de Convenio C - ASESOR FC
										
									), //Codigo de escalafon 15
                                array('1'=>'+','2'=>'+','3'=>'-','4'=>'+','5'=>'-','6'=>'-','7'=>'-','8'=>'-') //Signo Concepto en base al id 16     

                            );

    const __ESCALAFON_CONTRATO_LOCACION_SERVICIOS__ = 920;	
    const __REMUNERACION_MAXIMA_LOCACION_SERVICIOS__ = 9500;
    const __REMUNERACION_MINIMA_LOCACION_SERVICIOS__ = 1800;
    protected static $contratoLocacionServicios = array(
        9500 => array('cat' => 200, 'leyenda' => 'Responsable de Proyecto II'),
        9000 => array('cat' => 201, 'leyenda' => 'Responsable de Proyecto I'),
        8000 => array('cat' => 202, 'leyenda' => 'Coordinador IV'),
        7500 => array('cat' => 203, 'leyenda' => 'Coordinador III'),
        7000 => array('cat' => 204, 'leyenda' => 'Coordinador II'),
        6500 => array('cat' => 205, 'leyenda' => 'Coordinador I'),
        6000 => array('cat' => 206, 'leyenda' => 'Consultor Experto IV'),
        5600 => array('cat' => 207, 'leyenda' => 'Consultor Experto III'),
        5200 => array('cat' => 208, 'leyenda' => 'Consultor Experto II'),
        4800 => array('cat' => 209, 'leyenda' => 'Consultor Experto I'),
        4400 => array('cat' => 210, 'leyenda' => 'Consultor IV'),
        4100 => array('cat' => 211, 'leyenda' => 'Consultor III'),
        3800 => array('cat' => 212, 'leyenda' => 'Consultor II'),
        3500 => array('cat' => 213, 'leyenda' => 'Consultor I'),
        2600 => array('cat' => 214, 'leyenda' => 'Administrativo III'),
        2200 => array('cat' => 215, 'leyenda' => 'Administrativo II'),
        1800 => array('cat' => 216, 'leyenda' => 'Administrativo I'),
    );

    public static function getCodigoSirhu($id, $categoria) 
    {
        /*
         * Voy a buscar el grado y escalafon a la tabla subcategoria y no mas al array 
         * harcodeado de esta clase (son los index del array 14 y 15 respectivamente)
         * @gluis - 27/10/2017
         */
        $rsm = new ResultSetMapping();
        
        $rsm->addScalarResult('sirhu_grado', 'sirhu_grado');
        $rsm->addScalarResult('sirhu_escalafon', 'sirhu_escalafon');
        
        if (self::$em == null) {
            throw new \Exception("El entity manager no ha sido seteado a ConstanteSirhu.");
        }
        
        $query = self::$em->createNativeQuery('SELECT sirhu_grado, sirhu_escalafon FROM subcategoria WHERE id = :id', $rsm);
        
        $query->setParameter('id', $id);
        
        $subcategoria = $query->getOneOrNullResult();
        if ($subcategoria) {
            switch($categoria) {
                case self::INDEX_GRADO:
                    return $subcategoria['sirhu_grado'];
                    break;
                case self::INDEX_ESCALAFON:
                    return $subcategoria['sirhu_escalafon'];
                    break;
            }

            if(isset(self::$vec[$categoria][$id])) {
                return self::$vec[$categoria][$id]; 
            } else {
                return 0;
            }
            
        } else {
            return 0;
        }
    }
    
    public static function getCategoriaContratoLocacionServicios($remuneracionContrato)
    {
        ksort(self::$contratoLocacionServicios);
		if ($remuneracionContrato > self::__REMUNERACION_MAXIMA_LOCACION_SERVICIOS__) {
			return self::$contratoLocacionServicios[self::__REMUNERACION_MAXIMA_LOCACION_SERVICIOS__];
		}

		if ($remuneracionContrato < self::__REMUNERACION_MINIMA_LOCACION_SERVICIOS__) {
			return self::$contratoLocacionServicios[self::__REMUNERACION_MINIMA_LOCACION_SERVICIOS__];
		}
		
        foreach(self::$contratoLocacionServicios as $remuneracionItem => $item) {
           
            if ($remuneracionContrato <= $remuneracionItem) {
                return $item;
            }
        }
    }
    
    public static function setEntityManager($em) 
    {
        self::$em = $em;
    }
}