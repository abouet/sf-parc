<?php

namespace App\Controller;

use FOS\RestBundle\View\View;

/**
 *
 * @author abouet
 */
interface RESTPostInterface {

    public function new(): View;
}
