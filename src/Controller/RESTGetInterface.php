<?php

namespace App\Controller;

use FOS\RestBundle\View\View;

/**
 *
 * @author abouet
 */
interface RESTGetInterface {

    public function first(): view;

    public function last(): view;

    public function count(): view;

    public function list(): View;

    public function get($id): View;
}
