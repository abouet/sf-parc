<?php

namespace App\Controller;

use App\Controller\RESTGetInterface;
use App\Controller\RESTDeleteInterface;
use App\Controller\RESTPatchInterface;
use App\Controller\RESTPostInterface;
use App\Controller\RESTPutInterface;
use App\Controller\RESTGetTrait;
use App\Controller\RESTDeleteTrait;
use App\Controller\RESTPatchTrait;
use App\Controller\RESTPostTrait;
use App\Controller\RESTPutTrait;
use FOS\RestBundle\Controller\Annotations as REST;
use OpenApi\Annotations as OA;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ResourceServiceInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Implémente la méthode Rest GET, mots réservés et sélection de ressources
 *
 * @author abouet
 */
abstract class AbstractRestController extends AbstractFOSRestController implements RESTGetInterface, RESTDeleteInterface, RESTPatchInterface, RESTPostInterface, RESTPutInterface {

    use RESTGetTrait,
        RESTDeleteTrait,
        RESTPatchTrait,
        RESTPostTrait,
        RESTPutTrait;

    protected $service,
            $request;

    public function __construct(ResourceServiceInterface $service) {
        $this->service = $service;
    }

    protected function getRequest() {
        if (!isset($this->request)) {
            $this->request = $this->container->get('request_stack')->getCurrentRequest();
        }
        return $this->request;
    }

}
