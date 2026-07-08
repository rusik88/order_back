<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Manager\Order\OrderRequest;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderApiController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(!$this->hasAccess($request, 'order:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        $perPage = (int) $request->query('per_page', 10);
        $filter_name = $request->query('name');

        $sortField = $request->query('sort_field', 'name');
        $sortDirection = $request->query('sort_direction', 'asc');

        $query = OrderStatus::query()
            ->when($filter_name, function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when(in_array($sortField, ['name', 'order_status_id', 'id', 'total', 'created_at']), function ($query) use ($sortField, $sortDirection) {
                $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
            });

        $query->where('user_id', $request->user()->id);

        $orders = $perPage === -1 ? $query->get() : $query->paginate($perPage);

        return $this->success([
            'orders' => $perPage !== -1 ? $orders->items() : $orders,
            'paginate' => [
                'current_page' => $perPage !== -1 ? $orders->currentPage() : 1,
                'from' => $perPage !== -1 ? $orders->firstItem() : 1,
                'last_page' => $perPage !== -1 ? $orders->lastPage() : 1,
                'per_page' => $perPage !== -1 ? $orders->perPage() : -1,
                'to' => $perPage !== -1 ? $orders->lastItem() : count($orders),
                'total' => $perPage !== -1 ? $orders->total() : count($orders)
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        if(!$this->hasAccess($request, 'order:create')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        try {
            $order = Order::create([
                    "name"              => $request->name,
                    "user_id"           => $request->user()->id,
                    "order_status_id"   => $request->order_status_id,
                    "total"             => $request->total,
                    "comment"           => $request->comment
                ]
            );
            return $this->success([
                'order' => $order,
            ], 'Order created successfully', Response::HTTP_CREATED);

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if(!$this->hasAccess($request, 'order:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        $order = Order::find($id);

        if (!$order) {
            return $this->error('Order not found', Response::HTTP_NOT_FOUND);
        }

        $order->load('user');
        $order->load('order_status');

        return $this->success([
            'order' => $order,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(!$this->hasAccess($request, 'order:update')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        try {
            $order = Order::find($id);

            if (!$order) {
                return $this->error('Order not found', Response::HTTP_NOT_FOUND);
            }

            $order->update([
                "name"              => $request->name,
                "order_status_id"   => $request->order_status_id,
                "total"             => $request->total,
                "comment"           => $request->comment
            ]);

            return $this->success([
                'order' => $order,
            ], 'Order updated successfully');

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if(!$this->hasAccess($request, 'order:delete')) {
            return $this->error(
                'Access denied.',
                403
            );
        }
        try {
            $order = Order::find($id);

            if (!$order) {
                return $this->error('Order not found', Response::HTTP_NOT_FOUND);
            }

            $order->delete();

            return $this->success(
                [],
                'Order deleted successfully'
            );

        } catch(\Exception $err) {
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
