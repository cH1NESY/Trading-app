<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\OrderService;
use App\DTO\OrderCreateDTO;
use App\DTO\OrderUpdateDTO;
use App\DTO\OrderFilterDTO;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;

class OrderController extends Controller
{
    public function showList(Request $request, OrderService $orderService)
    {
        $dto = new OrderFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $orders = $orderService->getOrdersWithFilters($dto, $perPage);
        $warehouses = Warehouse::all();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        }

        return view('orders.index', compact('orders', 'warehouses'));
    }

    public function showCreate()
    {
        $warehouses = Warehouse::all();
        $products = Product::with(['stocks.warehouse'])->get();
        return view('orders.create', compact('warehouses', 'products'));
    }

    public function store(OrderCreateRequest $request, OrderService $orderService)
    {
        $dto = new OrderCreateDTO($request->validated());
        $order = $orderService->createOrder($dto);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Заказ успешно создан'
            ], 201);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно создан');
    }

    public function showOne(Order $order, Request $request)
    {
        $order->load(['warehouse', 'items.product']);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        
        return view('orders.show', compact('order'));
    }

    public function showEdit(Order $order, OrderService $orderService)
    {
        $order = $orderService->getOrderForEdit($order);
        $order->load(['warehouse', 'items.product']);
        $warehouses = Warehouse::all();
        $products = Product::with(['stocks.warehouse'])->get();
        
        return view('orders.edit', compact('order', 'warehouses', 'products'));
    }

    public function update(OrderUpdateRequest $request, Order $order, OrderService $orderService)
    {
        $order = $orderService->getActiveOrder($order);
        $dto = new OrderUpdateDTO($request->validated());
        $order = $orderService->updateOrder($order, $dto);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Заказ успешно обновлен'
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно обновлен');
    }

    public function complete(Order $order, Request $request, OrderService $orderService)
    {
        $order = $orderService->completeOrder($order);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Заказ успешно завершен'
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно завершен');
    }

    public function cancel(Order $order, Request $request, OrderService $orderService)
    {
        $order = $orderService->cancelOrder($order);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Заказ успешно отменен'
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно отменен');
    }

    public function resume(Order $order, Request $request, OrderService $orderService)
    {
        $order = $orderService->resumeOrder($order);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Заказ успешно возобновлен'
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Заказ успешно возобновлен');
    }
} 