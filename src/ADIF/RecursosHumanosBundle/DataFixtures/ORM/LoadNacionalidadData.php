<?php

namespace ADIF\RecursosHumanosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadNacionalidadData
 * 
 * @author Manuel Becerra
 * created 09/11/2015
 *
 */
class LoadNacionalidadData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $nombre
     */
    private function setData($manager, $nombre) {

        $entity = new \ADIF\RecursosHumanosBundle\Entity\Nacionalidad();

        $entity->setNombre($nombre);

        $manager->persist($entity);
    }

    public function load(ObjectManager $manager) {

        $this->setData($manager, "Mexicana");
        $this->setData($manager, "Afgana");
        $this->setData($manager, "Albanesa");
        $this->setData($manager, "Alemana");
        $this->setData($manager, "Alto volteña");
        $this->setData($manager, "Andorrana");
        $this->setData($manager, "Angoleña");
        $this->setData($manager, "Argelina");
        $this->setData($manager, "Argentina");
        $this->setData($manager, "Australiana");
        $this->setData($manager, "Austriaca");
        $this->setData($manager, "Bahamesa");
        $this->setData($manager, "Bahreina");
        $this->setData($manager, "Bangladesha");
        $this->setData($manager, "Barbadesa");
        $this->setData($manager, "Belga");
        $this->setData($manager, "Beliceña");
        $this->setData($manager, "Bermudesa");
        $this->setData($manager, "Birmana");
        $this->setData($manager, "Boliviana");
        $this->setData($manager, "Botswanesa");
        $this->setData($manager, "Brasileña");
        $this->setData($manager, "Bulgara");
        $this->setData($manager, "Burundesa");
        $this->setData($manager, "Butana");
        $this->setData($manager, "Camboyana");
        $this->setData($manager, "Camerunesa");
        $this->setData($manager, "Canadiense");
        $this->setData($manager, "Centroafricana");
        $this->setData($manager, "Chadeña");
        $this->setData($manager, "Checoslovaca");
        $this->setData($manager, "Chilena");
        $this->setData($manager, "China");
        $this->setData($manager, "Chipriota");
        $this->setData($manager, "Colombiana");
        $this->setData($manager, "Congoleña");
        $this->setData($manager, "Costarricense");
        $this->setData($manager, "Cubana");
        $this->setData($manager, "Dahoneya");
        $this->setData($manager, "Danes");
        $this->setData($manager, "Dominicana");
        $this->setData($manager, "Ecuatoriana");
        $this->setData($manager, "Egipcia");
        $this->setData($manager, "Emirata");
        $this->setData($manager, "Escosesa");
        $this->setData($manager, "Eslovaca");
        $this->setData($manager, "Española");
        $this->setData($manager, "Estona");
        $this->setData($manager, "Etiope");
        $this->setData($manager, "Fijena");
        $this->setData($manager, "Filipina");
        $this->setData($manager, "Finlandesa");
        $this->setData($manager, "Francesa");
        $this->setData($manager, "Gabiana");
        $this->setData($manager, "Gabona");
        $this->setData($manager, "Galesa");
        $this->setData($manager, "Ghanesa");
        $this->setData($manager, "Granadeña");
        $this->setData($manager, "Griega");
        $this->setData($manager, "Guatemalteca");
        $this->setData($manager, "Guinesa Ecuatoriana");
        $this->setData($manager, "Guinesa");
        $this->setData($manager, "Guyanesa");
        $this->setData($manager, "Haitiana");
        $this->setData($manager, "Holandesa");
        $this->setData($manager, "HondureÑa");
        $this->setData($manager, "Hungara");
        $this->setData($manager, "India");
        $this->setData($manager, "Indonesa");
        $this->setData($manager, "Inglesa");
        $this->setData($manager, "Iraki");
        $this->setData($manager, "Irani");
        $this->setData($manager, "Irlandesa");
        $this->setData($manager, "Islandesa");
        $this->setData($manager, "Israeli");
        $this->setData($manager, "Italiana");
        $this->setData($manager, "Jamaiquina");
        $this->setData($manager, "Japonesa");
        $this->setData($manager, "Jordana");
        $this->setData($manager, "Katensa");
        $this->setData($manager, "Keniana");
        $this->setData($manager, "Kuwaiti");
        $this->setData($manager, "Laosiana");
        $this->setData($manager, "Leonesa");
        $this->setData($manager, "Lesothensa");
        $this->setData($manager, "Letonesa");
        $this->setData($manager, "Libanesa");
        $this->setData($manager, "Liberiana");
        $this->setData($manager, "LibeÑa");
        $this->setData($manager, "Liechtenstein");
        $this->setData($manager, "Lituana");
        $this->setData($manager, "Luxemburgo");
        $this->setData($manager, "Madagascar");
        $this->setData($manager, "Malaca");
        $this->setData($manager, "Malawi");
        $this->setData($manager, "Maldivas");
        $this->setData($manager, "Mali");
        $this->setData($manager, "Maltesa");
        $this->setData($manager, "Marfilesa");
        $this->setData($manager, "Marroqui");
        $this->setData($manager, "Mauricio");
        $this->setData($manager, "Mauritana");
        $this->setData($manager, "Monaco");
        $this->setData($manager, "Mongolesa");
        $this->setData($manager, "Nauru");
        $this->setData($manager, "Neozelandesa");
        $this->setData($manager, "Nepalesa");
        $this->setData($manager, "Nicaraguense");
        $this->setData($manager, "Nigerana");
        $this->setData($manager, "Nigeriana");
        $this->setData($manager, "Norcoreana");
        $this->setData($manager, "Norirlandesa");
        $this->setData($manager, "Norteamericana");
        $this->setData($manager, "Noruega");
        $this->setData($manager, "Omana");
        $this->setData($manager, "Pakistani");
        $this->setData($manager, "Panameña");
        $this->setData($manager, "Paraguaya");
        $this->setData($manager, "Peruana");
        $this->setData($manager, "Polaca");
        $this->setData($manager, "Portoriqueña");
        $this->setData($manager, "Portuguesa");
        $this->setData($manager, "Rhodesiana");
        $this->setData($manager, "Ruanda");
        $this->setData($manager, "Rumana");
        $this->setData($manager, "Rusa");
        $this->setData($manager, "Salvadoreña");
        $this->setData($manager, "Samoa Occidental");
        $this->setData($manager, "San marino");
        $this->setData($manager, "Saudi");
        $this->setData($manager, "Senegalesa");
        $this->setData($manager, "Sikkim");
        $this->setData($manager, "Singapur");
        $this->setData($manager, "Siria");
        $this->setData($manager, "Somalia");
        $this->setData($manager, "Sovietica");
        $this->setData($manager, "Sri Lanka");
        $this->setData($manager, "Suazilandesa");
        $this->setData($manager, "Sudafricana");
        $this->setData($manager, "Sudanesa");
        $this->setData($manager, "Sueca");
        $this->setData($manager, "Suiza");
        $this->setData($manager, "Surcoreana");
        $this->setData($manager, "Tailandesa");
        $this->setData($manager, "Tanzana");
        $this->setData($manager, "Tonga");
        $this->setData($manager, "Tongo");
        $this->setData($manager, "Trinidad y Tobago");
        $this->setData($manager, "Tunecina");
        $this->setData($manager, "Turca");
        $this->setData($manager, "Ugandesa");
        $this->setData($manager, "Uruguaya");
        $this->setData($manager, "Vaticano");
        $this->setData($manager, "Venezolana");
        $this->setData($manager, "Vietnamita");
        $this->setData($manager, "Yemen Rep Arabe");
        $this->setData($manager, "Yemen Rep Dem");
        $this->setData($manager, "Yugoslava");
        $this->setData($manager, "Zaire");

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

}
