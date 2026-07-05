<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Api\Manager\OrderStatus\StoreOrderStatusRequest;
use App\Http\Requests\Api\Manager\OrderStatus\UpdateOrderStatusRequest;
use App\Models\OrderStatus;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class OrderStatusApiController extends AbstractApiController
{
    use ApiResponseTrait;

    #[OA\Get(
        path: "/api/order_statuses",
        summary: "Get paginated Order Statuses list",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Order Statuses"],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "per_page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 10)
            ),
            new OA\Parameter(
                name: "name",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: '')
            ),
            new OA\Parameter(
                name: "sort_field",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: 'id')
            ),
            new OA\Parameter(
                name: "sort_direction",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: 'asc')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Order Statuses list",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "order_statuses",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "integer"),
                                            new OA\Property(property: "name", type: "string"),
                                            new OA\Property(property: "slug", type: "string"),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: "paginate",
                                    properties: [
                                        new OA\Property(property: "current_page", type: "integer"),
                                        new OA\Property(property: "from", type: "integer"),
                                        new OA\Property(property: "last_page", type: "integer"),
                                        new OA\Property(property: "per_page", type: "integer"),
                                        new OA\Property(property: "to", type: "integer"),
                                        new OA\Property(property: "total", type: "integer"),
                                    ],
                                    type: "object"
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $filter_name = $request->query('name');

        $sortField = $request->query('sort_field', 'name');
        $sortDirection = $request->query('sort_direction', 'asc');

        $query = OrderStatus::query()
            ->when($filter_name, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when(in_array($sortField, ['name', 'slug', 'id', 'created_at']), function ($query) use ($sortField, $sortDirection) {
                $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
            });

        $order_statuses = $perPage === -1 ? $query->get() : $query->paginate($perPage);

        return $this->success([
            'order_statuses' => $perPage !== -1 ? $order_statuses->items() : $order_statuses,
            'paginate' => [
                'current_page' => $perPage !== -1 ? $order_statuses->currentPage() : 1,
                'from' => $perPage !== -1 ? $order_statuses->firstItem() : 1,
                'last_page' => $perPage !== -1 ? $order_statuses->lastPage() : 1,
                'per_page' => $perPage !== -1 ? $order_statuses->perPage() : -1,
                'to' => $perPage !== -1 ? $order_statuses->lastItem() : count($order_statuses),
                'total' => $perPage !== -1 ? $order_statuses->total() : count($order_statuses)
            ]
        ], 200);
    }

    #[OA\Post(
        path: "/api/order_statuses",
        summary: "Create Order Statuses",
        security: [
            ["bearerAuth" => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "slug"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Administrator"),
                    new OA\Property(property: "slug", type: "string", example: "admin"),
                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["users.view", "users.create"]
                    )
                ]
            )
        ),
        tags: ["Order Statuses"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Order Status created",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Order Status created successfully"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "order_statuses",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer"),
                                        new OA\Property(property: "name", type: "string"),
                                        new OA\Property(property: "slug", type: "string"),
                                    ],
                                    type: "object"
                                )
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function store(StoreOrderStatusRequest $request): JsonResponse
    {
        try {
            $order_status = OrderStatus::create([
                    "name" => $request->name,
                    "slug" => $request->slug
                ]
            );
            return $this->success([
                'order_status' => $order_status,
            ], 'Order Status created successfully', Response::HTTP_CREATED);

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Get(
        path: "/api/order_statuses/{id}",
        summary: "Get Order Status by id",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Order Statuses"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Order Status found",
            ),
            new OA\Response(
                response: 404,
                description: "Order Status not found"
            )
        ]
    )]
    public function show(string $id)
    {
        $order_status = OrderStatus::find($id);

        if (!$order_status) {
            return $this->error('Order Status not found', Response::HTTP_NOT_FOUND);
        }

        return $this->success([
            'order_status' => $order_status,
        ]);
    }

    #[OA\Patch(
        path: "/api/order_statuses/{id}",
        summary: "Update Order Status by id",
        security: [
            ["bearerAuth" => []]
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Admin"),
                    new OA\Property(property: "slug", type: "string", example: "admin"),
                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        items: new OA\Items(type: "string"),
                        example: ["users.view"]
                    )
                ]
            )
        ),
        tags: ["Order Statuses"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Updated"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function update(UpdateOrderStatusRequest $request, string $id): JsonResponse
    {
        try {
            $order_status = OrderStatus::find($id);

            if (!$order_status) {
                return $this->error('Order Status not found', Response::HTTP_NOT_FOUND);
            }

            $order_status->update([
                "name" => $request->name,
                "slug" => $request->slug
            ]);

            return $this->success([
                'order_status' => $order_status,
            ], 'Order Status updated successfully');

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[OA\Delete(
        path: "/api/order_statuses/{id}",
        summary: "Delete Order Status by id",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Order Statuses"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted"),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        try {
            $order_status = OrderStatus::find($id);

            if (!$order_status) {
                return $this->error('Order Status not found', Response::HTTP_NOT_FOUND);
            }

            $order_status->delete();

            return $this->success(
                [],
                'Order Status deleted successfully'
            );

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
