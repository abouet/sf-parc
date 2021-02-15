<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Context\Context;
use Hateoas\HateoasBuilder;
use Doctrine\Common\Collections\Criteria;
use OpenApi\Annotations as OA;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
use App\Util\Converter\ObjectReader;
use App\Util\Converter\ObjectConverter;

trait RESTGetTrait {

    static protected $queryReservedWords = ['fields', 'filter', 'sort', 'desc'];
    static protected $paginationReservedWords = ['page', 'limit'];
    static protected $pathReservedWords = ['count', 'first', 'last'];
    protected $fields = [];

    /**
     * @REST\Get(
     *     "/first",
     *     name="_first",
     *     schemes="https"
     * )
     * @OA\Get(
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function first(): View {
        $view = $this->initListView();
        $this->processRequest($view);
        $content = $this->service->find();
        $this->processPartialResponse($content, $view);
        var_dump(json_encode($content));
        return $view->setData($content);
    }

    /**
     * @REST\Get(
     *     "/count",
     *     name="_count",
     *     schemes="https"
     * )
     * @OA\Get(
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\Schema(
     *            type="integer"
     *         ) 
     *     )
     * )
     * 
     */
    public function count(): View {
        $view = $this->initListView();
        $this->filterCriteria();
        return $view->setData($this->service->count());
    }

    /**
     * @REST\Get(
     *     "/last",
     *     name="_last",
     *     schemes="https"
     * )
     * @OA\Get(
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function last(): View {
        $view = $this->initListView();
        $this->processRequest($view);
        $content = $this->service->find();
        $this->processPartialResponse($content, $view);
        return $view->setData($content);
    }

    /**
     * @REST\Get(
     *     "",
     *     name="_list",
     *     schemes="https"
     * )
     * @OA\Get(
     *     summary="get a list of RESOURCE",
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response="206",
     *         description="Partial Content if pagination"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No resource found"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Server error or invalid request"
     *     )
     * )
     */
    public function list(): View {
        $view = $this->initListView();
        $criteria = new Criteria();
        if ($this->processRequest($view) == true) {
            $list = $this->service->find();
        } else {
            $list = $this->service->getAll();
        }
        $this->processPartialResponse($list, $view);
        $content = $this->processPagination($list, $view);
        return $view->setData($content);
    }

    /**
     * @REST\Get(
     *     "/{id}",
     *     name="_get",
     *     schemes="https"
     * )
     * @OA\Get(
     *     summary="get a RESOURCE",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The resource id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success"   
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"   
     *     )
     * )
     * OA\XmlContent(
     *         OA\Schema(type="object",
     *             OA\Property(property="id", ref=Model(type=App\Model\Plateform\Plugin::class))
     *         )
     *     )
     * 
     * @return View
     */
    public function get($id): View {
        $view = new View(null, Response::HTTP_OK);
        $content = $this->service->get($id);
        $this->processPartialResponse($content, $view);
        return $view->setData($content);
    }

    /**
     * @return View
     */
    protected function initListView(): View {
        $view = new View([], Response::HTTP_OK);
        $context = new Context();
        $context->enableMaxDepth();
        $view->setContext($context);
        return $view;
    }

    /**
     * Process the HTTP Request according on the reserved words in the query
     * 
     * @param View $view
     * @param QueryBuilder $criteria
     */
    protected function processRequest(View &$view) {
        $result = false;
        if ($this->isReservedPath()) {
            switch ($this->getReservedPath()) {
                case 'first':
                    $this->service->addOffset(0);
                    $this->service->addLimit(1);
                    break;
                case 'last':
                    $this->service->addOffset($this->service->count([]) - 1);
                    $this->service->addLimit(1);
                    break;
            }
        }
        If ($this->getRequest()->query->has('sort')) {
            $this->processOrderBy('sort');
            $result = true;
        }
        If ($this->getRequest()->query->has('desc')) {
            $this->processOrderBy('desc');
            $result = true;
        }
        $status = $this->filterCriteria();
        $result = ($status === true & $result === false) ? true : $result;
        return $result;
    }

    protected function processOrderBy($order) {
        switch ($order) {
            case 'sort':
                $orderClause = Criteria::ASC;
                break;
            case 'desc':
                $orderClause = Criteria::DESC;
                break;
        }
        try {
            $fields = (array) explode(",", $this->getRequest()->query->get($order));
            $this->service->validateFields($fields);
            forEach ($fields as $idx => $field) {
                $this->service->addOrderBy($field, $orderClause);
            }
        } catch (\Exception $ex) {
            throw new \Exception(sprintf("%s in 'desc' HTTP parameter", $ex->getMessage()));
        }
        return true;
    }

    /**
     * Pagination is mandatory
     * Rest reserved word : range in query
     * http code = 206 | Partial Content if count > end range
     * 
     * @example ?range=10-15
     * @param View $view
     * @param Request $this->getRequest()
     * @param QueryBuilder $criteria
     */
    protected function processPagination($list, View &$view) {
        if (!$this->getRequest()->query->has('page') | !$this->getRequest()->query->has('limit')) {
            throw new \InvalidArgumentException(sprintf("Missing word for pagination : %s", implode(', ', self::$paginationReservedWords)));
        }
        $page = $this->getRequest()->query->get('page');
        $limit = $this->getRequest()->query->get('limit');
        $count = count($list);
        $pages = (int) ceil($count / $limit);
        $chunks = array_chunk($list, $limit);
        $content = new PaginatedRepresentation(
                new CollectionRepresentation($chunks[$page - 1]),
                $this->getRequest()->get('_route'),
                [],
                $page, // page number
                $limit, // limit
                $pages, // total pages
                'page', // page route parameter name, optional, defaults to 'page'
                'limit', // limit route parameter name, optional, defaults to 'limit'
                false, // generate relative URIs, optional, defaults to `false`
                $count  // total collection size, optional, defaults to `null`
        );
        return $content;
    }

    /**
     * Reserved word : fields (PARTIAL RESPONSES)
     * 
     * @example : ?fields=field1,field2
     * @param View $view set Response::HTTP_PARTIAL_CONTENT is needed
     */
    protected function processPartialResponse(&$content, View &$view) {
        If ($this->getRequest()->query->has('fields')) {
            try {
                $fields = (array) explode(",", $this->getRequest()->query->get('fields'));
                $this->service->validateFields($fields);
                $obj = new \stdClass();
                forEach ($fields as $field) {
                    $obj->$field = null;
                }
                $converter = new ObjectConverter($obj);
                $reader = new ObjectReader($converter);
                if (is_array($content)) {
                    foreach ($content as $key => $model) {
                        $reader->parse($model);
                        $content[$key] = $converter->getResult();
                    }
                } elseif (is_object($content)) {
                    //var_dump($content);
                    $reader->parse($content);
                    $content = $converter->getResult();
                }
                $view->setStatusCode(Response::HTTP_PARTIAL_CONTENT);
                return true;
            } catch (\Exception $ex) {
                $exception = new \InvalidArgumentException(sprintf("%s in 'fields' HTTP parameter", $ex->getMessage()));
                throw $exception;
            }
        }
        return false;
    }

    /**
     * Filtrer la liste en fonction des champs de la requête HTTP
     * 
     * @param View $view
     * @param Request $this->getRequest()
     * @param QueryBuilder $criteria
     */
    protected function filterCriteria(): bool {

        $result = false;
        // Suppression des mots clés déjà traités de la requête HTTP
        $requestParams = $this->getRequest()->query->all();
        $queryReservedWords = array_merge(self::$queryReservedWords, self::$paginationReservedWords);
        forEach ($queryReservedWords as $param) {
            unset($requestParams[$param]);
        }
        // Filtrer la liste
        $this->service->validateFields(array_keys($requestParams));
        forEach ($requestParams as $field => $value) {
            if (in_array($value, array('true', 'false'))) {
                $value = str_replace(array('true', 'false'), array(1, 0), strtolower($value));
            }
            $this->service->addWhere($field, $value);
            $result = true;
        }
        return $result;
    }

    protected function isReservedPath(): bool {
        $array = explode("/", $this->getRequest()->getpathInfo());
        $last = end($array);
        return in_array($last, self::$pathReservedWords);
    }

    protected function getReservedPath() {
        $array = explode("/", $this->getRequest()->getpathInfo());
        return end($array);
    }

}
