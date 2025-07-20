<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\DTO\OrderCreateDTO;
use App\DTO\OrderUpdateDTO;
use App\DTO\OrderFilterDTO;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;

class OrderController extends Controller
{
    public function index(Request $request, OrderService $orderService)
    {
        $dto = new OrderFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $orders = $orderService->getOrdersWithFilters($dto, $perPage);
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function store(OrderCreateRequest $request, OrderService $orderService)
    {
        $dto = new OrderCreateDTO($request->validated());
        $order = $orderService->createOrder($dto);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно создан'], 201);
    }

    public function show(Order $order)
    {
        $order->load(['warehouse', 'items.product']);
        return response()->json(['success' => true, 'data' => $order]);
    }

    public function update(OrderUpdateRequest $request, Order $order, OrderService $orderService)
    {
        $order = $orderService->getActiveOrder($order);
        $dto = new OrderUpdateDTO($request->validated());
        $order = $orderService->updateOrder($order, $dto);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно обновлен']);
    }

    public function complete(Order $order, OrderService $orderService)
    {
        $order = $orderService->completeOrder($order);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно завершен']);
    }

    public function cancel(Order $order, OrderService $orderService)
    {
        $order = $orderService->cancelOrder($order);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно отменен']);
    }

    public function resume(Order $order, OrderService $orderService)
    {
        $order = $orderService->resumeOrder($order);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно возобновлен']);
    }
} 