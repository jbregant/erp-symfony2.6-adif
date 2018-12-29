<?php

namespace ADIF\AutenticacionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ADIFAutenticacionBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
