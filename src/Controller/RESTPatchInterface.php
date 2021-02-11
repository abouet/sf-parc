<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\Serializer\SerializerInterface;

/**
 *
 * @author abouet
 */
interface RESTPatchInterface {

    public function update($id, SerializerInterface $serializer): View;
}
