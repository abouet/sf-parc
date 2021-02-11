<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\Serializer\SerializerInterface;

/**
 *
 * @author abouet
 */
interface RESTPutInterface {

    public function edit($id, SerializerInterface $serializer): View;
}
