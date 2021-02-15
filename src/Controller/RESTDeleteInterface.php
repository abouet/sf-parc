<?php

namespace App\Controller;

use FOS\RestBundle\View\View;

/**
 *
 * @author abouet
 */
interface RESTDeleteInterface {

    public function remove($id): View;
}
