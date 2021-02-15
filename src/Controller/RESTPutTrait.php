<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

trait RESTPutTrait {

    /**
     * @REST\Put(
     *     "/{id}",
     *     name="_edit",
     *     schemes="https"
     * )
     * @OA\Put(
     *     summary="edit a RESOURCE",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The resource id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="The new resource",
     *         required=true,
     *         @OA\JsonContent(ref=@Model(type=RESOURCE)),
     *         @OA\XmlContent(ref=@Model(type=RESOURCE))
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Full update",
     *         @OA\JsonContent(),
     *         @OA\XmlContent()
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     )
     * )
     * @return View
     */
    public function edit($id, SerializerInterface $serializer): View {
        $update = $serializer->deserialize($this->getRequest()->getContent(), $this->service->getModelClassName(), $this->getRequest()->getContentType());
        $ressource = $this->service->update($id, $update);
        return View::create($ressource, Response::HTTP_OK);
    }

}
