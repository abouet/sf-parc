<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as REST;
use OpenApi\Annotations as OA;

/**
 *
 * @author abouet
 */
trait RESTDeleteTrait {

    /**
     * @REST\Delete(
     *     "/{id}",
     *     name="_delete",
     *     schemes="https"
     * )
     * @OA\Delete(
     *     summary="Delete a {ressource}",
     *     @OA\Parameter(
     *         name="api_key",
     *         in="header",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The resource id"
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="Deletion successful"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     * )
     * 
     * @return View
     */
    public function remove($id): View {
        if ($this->service->delete($id)) {
            return View::create([], Response::HTTP_NO_CONTENT);
        } else {
            return View::create([], Response::HTTP_BAD_REQUEST);
        }
    }

}
