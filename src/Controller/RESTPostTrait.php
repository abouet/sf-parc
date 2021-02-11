<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

trait RESTPostTrait {

    /**
     * @REST\Post(
     *     "",
     *     name="_new",
     *     schemes="https"
     * )
     * @OA\Post(
     *     summary="Create a new resource",
     *     @OA\RequestBody(
     *         description="The new resource",
     *         required=true,
     *         @OA\JsonContent(ref=@Model(type=App\Model\Hardware\Phone::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\MediaType(
     *                 mediaType="application/ld+json"
     *             ),
     *             @OA\MediaType(
     *                 mediaType="application/vnd.api+json"
     *             )
     *        ),
     *        @OA\Items(ref=@Model(type=App\Model\Hardware\Phone::class))
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     )
     * )
     * @return View
     */
    public function new($ressource = null): View {
        if (is_null($ressource)) {
            $ressource = $this->service->create($this->getRequest()->request->all());
        } else {
            $ressource = $this->service->create($ressource);
        }
        return View::create($ressource, Response::HTTP_CREATED);
    }

}
