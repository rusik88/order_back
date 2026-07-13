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

class OrderStatusApiController extends AbstractApiController
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {

            if(!$this->hasAccess($request, 'order_status:read')) {
                return $this->error(
                    'Access denied.',
                    403
                );
            }
        try {
            $perPage = (int) $request->query('per_page', 10);
            $filter_name = $request->query('name');

            $sortField = $request->query('sort_field', 'id');
            $sortDirection = $request->query('sort_direction', 'desc');

            $query = OrderStatus::query()
                ->when($filter_name, function ($query, $name) {
                    $query->where('name', 'like', "%{$name}%");
                })
                ->when(in_array($sortField, ['name', 'slug', 'id', 'created_at']), function ($query) use ($sortField, $sortDirection) {
                    $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
                });

            $query->where('user_id', $request->user()->id);

            $order_statuses = $perPage === -1 ? $query->get() : $query->paginate($perPage);

            return $this->success([
                'order_statuses'    => $perPage !== -1 ? $order_statuses->items() : $order_statuses,
                'paginate'          => [
                    'current_page'  => $perPage !== -1 ? $order_statuses->currentPage() : 1,
                    'from'          => $perPage !== -1 ? $order_statuses->firstItem() : 1,
                    'last_page'     => $perPage !== -1 ? $order_statuses->lastPage() : 1,
                    'per_page'      => $perPage !== -1 ? $order_statuses->perPage() : -1,
                    'to'            => $perPage !== -1 ? $order_statuses->lastItem() : count($order_statuses),
                    'total'         => $perPage !== -1 ? $order_statuses->total() : count($order_statuses)
                ]
            ], 200);
        } catch(\Exception $err) {
            $this->log($request, "Order Status", "index", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreOrderStatusRequest $request): JsonResponse
    {
        if(!$this->hasAccess($request, 'order_status:create')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        try {
            $order_status = OrderStatus::create([
                    "name"      => $request->name,
                    "slug"      => $request->slug,
                    "user_id"   => $request->user()->id,
                ]
            );
            return $this->success([
                'order_status' => $order_status,
            ], 'Order Status created successfully', Response::HTTP_CREATED);

        } catch(\Exception $err) {
            $this->log($request, "Order Status", "store", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, string $id)
    {
        try {
            if(!$this->hasAccess($request, 'order_status:read')) {
                return $this->error(
                    'Access denied.',
                    403
                );
            }
            $order_status = OrderStatus::find($id);

            if (!$order_status) {
                return $this->error('Order Status not found', Response::HTTP_NOT_FOUND);
            }

            $order_status->load('user');

            return $this->success([
                'order_status' => $order_status,
            ]);
        } catch(\Exception $err) {
            $this->log($request, "Order Status", "store", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateOrderStatusRequest $request, string $id): JsonResponse
    {
        if(!$this->hasAccess($request, 'order_status:update')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
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
            $this->log($request, "Order Status", "update", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        if(!$this->hasAccess($request, 'order_status:delete')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
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
            $this->log($request, "Order Status", "destroy", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
